// Global variables
let isChatOpen = false;
let knowledgeBase = null;

document.addEventListener('DOMContentLoaded', () => {
  const chatIcon = document.getElementById('chatIcon');
  const chatContainer = document.getElementById('chatContainer');

  // Initialize chat container state
  chatContainer.style.display = 'none';
  chatContainer.style.opacity = '0';
  chatContainer.style.transform = 'translateY(20px)';

  // Open chat on icon click
  chatIcon.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleChat();
  });

  // Close chat if clicking outside
  document.addEventListener('click', (e) => {
    if (!chatContainer.contains(e.target) && !chatIcon.contains(e.target)) {
      if (isChatOpen) closeChat();
    }
  });

  // Listen for Enter key on input field
  document.getElementById('messageInput').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') handleSend();
  });
});

async function loadKnowledgeBase() {
  // Load the entire knowledge base from a single JSON file
  try {
    const response = await fetch('storage/questions.json');
    if (!response.ok) throw new Error('Error loading knowledge base');
    knowledgeBase = await response.json();
  } catch (error) {
    console.error('Error loading knowledge base:', error);
  }
}

function toggleChat() {
  const chatContainer = document.getElementById('chatContainer');
  isChatOpen = !isChatOpen;

  if (isChatOpen) {
    chatContainer.style.display = 'flex';
    setTimeout(() => {
      chatContainer.style.opacity = '1';
      chatContainer.style.transform = 'translateY(0)';
    }, 10);
    // Load the knowledge base if not already loaded then display sections
    if (!knowledgeBase) {
      loadKnowledgeBase().then(loadSections);
    } else {
      loadSections();
    }
  } else {
    closeChat();
  }
}

function closeChat() {
  const chatContainer = document.getElementById('chatContainer');
  chatContainer.style.opacity = '0';
  chatContainer.style.transform = 'translateY(20px)';
  setTimeout(() => {
    chatContainer.style.display = 'none';
  }, 300);
  isChatOpen = false;
}

function toggleSections() {
  const sectionsPanel = document.getElementById('sectionsPanel');
  const isVisible = window.getComputedStyle(sectionsPanel).display !== 'none';
  sectionsPanel.style.display = isVisible ? 'none' : 'block';
  document.querySelector('.sections-btn').setAttribute('aria-expanded', !isVisible);
}

function loadSections() {
  if (!knowledgeBase) return;

  const sectionList = document.getElementById('sectionList');
  sectionList.innerHTML = ''; // Clear existing content

  knowledgeBase.sections.forEach((section, index) => {
    const sectionCard = document.createElement('div');
    sectionCard.className = 'section-card';
    sectionCard.innerHTML = `<h4>${section.title}</h4>`;
    // Attach event listener for section click
    sectionCard.addEventListener('click', (e) => {
      e.stopPropagation();
      loadQuestions(index);
    });
    sectionList.appendChild(sectionCard);
  });

  // Ensure the sections panel is visible
  const sectionsPanel = document.getElementById('sectionsPanel');
  sectionsPanel.style.display = 'block';
}

function loadQuestions(sectionIndex) {
  if (!knowledgeBase) return;

  // Hide the sections panel
  const sectionsPanel = document.getElementById('sectionsPanel');
  sectionsPanel.style.display = 'none';

  const currentSection = knowledgeBase.sections[sectionIndex];
  const chatMessages = document.getElementById('chatMessages');
  chatMessages.innerHTML = ''; // Clear previous messages

  currentSection.questions.forEach((questionObj, qIndex) => {
    const questionDiv = document.createElement('div');
    questionDiv.className = 'message question-item';
    questionDiv.textContent = questionObj.question;
    // Attach click listener for question
    questionDiv.addEventListener('click', (e) => {
      e.stopPropagation();
      showAnswer(questionObj.answer);
    });
    chatMessages.appendChild(questionDiv);
  });

  // Scroll to the bottom after loading questions
  scrollChatToBottom();
}

function showAnswer(answer) {
  const chatMessages = document.getElementById('chatMessages');
  chatMessages.innerHTML = ''; // Clear messages before showing answer
  simulateTyping(answer);
}

function simulateTyping(answer) {
  const typingIndicator = document.getElementById('typingIndicator');
  const chatMessages = document.getElementById('chatMessages');

  typingIndicator.style.display = 'flex';

  let index = 0;
  const messageDiv = document.createElement('div');
  messageDiv.className = 'message bot-message';
  chatMessages.appendChild(messageDiv);

  const typingInterval = setInterval(() => {
    if (index < answer.length) {
      messageDiv.textContent += answer.charAt(index);
      index++;
      scrollChatToBottom();
    } else {
      clearInterval(typingInterval);
      typingIndicator.style.display = 'none';
    }
  }, 20);
}

function handleSend() {
  const input = document.getElementById('messageInput');
  const queryOriginal = input.value.trim();
  const query = normalizeText(queryOriginal);
  if (!query) return;

  appendMessage(queryOriginal, true);
  input.value = '';
  scrollChatToBottom();
  showTyping(true);

  // Search for best matching question across all sections
  const bestMatch = findBestMatch(query);
  showTyping(false);

  if (bestMatch) {
    simulateTyping(bestMatch.answer);
  } else {
    appendMessage("I couldn't find an answer to that question. Please try another query.", false);
  }
}

function findBestMatch(query) {
  if (!knowledgeBase) return null;
  let bestMatch = null;
  let bestScore = 0;

  knowledgeBase.sections.forEach(section => {
    section.questions.forEach(questionObj => {
      // Normalize the question text for better matching
      const questionText = normalizeText(questionObj.question);
      const score = scoreMatch(query, questionText);
      if (score > bestScore) {
        bestScore = score;
        bestMatch = questionObj;
      }
    });
  });

  return bestMatch;
}

function scoreMatch(query, text) {
  // Simple scoring: count the number of query words found in the text
  const queryWords = query.split(/\s+/);
  let score = 0;
  queryWords.forEach(word => {
    if (text.indexOf(word) !== -1) {
      score++;
    }
  });
  return score;
}

function normalizeText(text) {
  // Convert to lower case and remove punctuation for more consistent matching
  return text
    .toLowerCase()
    .replace(/[^\w\s]/gi, '')
    .replace(/\s+/g, ' ')
    .trim();
}

function appendMessage(message, isUser) {
  const chatMessages = document.getElementById('chatMessages');
  const messageDiv = document.createElement('div');
  messageDiv.className = isUser ? 'message user-message' : 'message bot-message';
  messageDiv.textContent = message;
  chatMessages.appendChild(messageDiv);
  scrollChatToBottom();
}

function showTyping(show) {
  const typingIndicator = document.getElementById('typingIndicator');
  typingIndicator.style.display = show ? 'flex' : 'none';
}

function scrollChatToBottom() {
  const chatMessages = document.getElementById('chatMessages');
  chatMessages.scrollTop = chatMessages.scrollHeight;
}
