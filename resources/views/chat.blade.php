<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>iStart Rajasthan Chat Assistant</title>
  <meta name="description" content="iStart Rajasthan Chat Assistant for Startup Support Program">
  <meta name="author" content="iStart Team">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Using Font Awesome via CDN. For production, consider bundling it locally. -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
</head>
<body>
  <!-- Chat Icon (Button) Without Logo -->
  <button class="chat-icon-container" id="chatIcon" aria-label="Open Chat Assistant">
    <div class="chat-icon">
      <div class="icon-circle">
        <!-- Display a Font Awesome chat icon instead of a logo -->
        <i class="fas fa-comment-dots chat-icon-symbol"></i>
      </div>
      <div class="pulse-ring"></div>
    </div>
    <span class="hover-text">Ask iStart Assistant</span>
  </button>

  <!-- Chat Container -->
  <section class="chat-container" id="chatContainer" role="complementary" aria-label="Chat Assistant">
    <!-- Chat Header -->
    <header class="chat-header">
      <div class="header-brand">
        <img src="{{ asset('images\main_logo_2.png') }}" class="header-logo" alt="iStart Logo" loading="lazy">
        <div class="header-text">
          <h2>iStart Rajasthan</h2>
          <p>Startup Support Program</p>
        </div>
      </div>
      <div class="header-decoration" aria-hidden="true"></div>
    </header>

    <!-- Sections Button -->
    <button class="sections-btn" onclick="toggleSections()" aria-expanded="false">
      <i class="fas fa-compass"></i> Explore Topics
      <div class="btn-hover-effect"></div>
    </button>

    <!-- Sections Panel -->
    <aside class="sections-panel" id="sectionsPanel" aria-label="Topics List">
      <div class="section-list" id="sectionList"></div>
    </aside>

    <!-- Chat Main Area (Scrollable) -->
    <div class="chat-main">
      <div class="chat-messages" id="chatMessages">
        <div class="welcome-message">
          <div class="bot-avatar">
            <img src="{{ asset('images\main_logo_2.png') }}" alt="Assistant Avatar" loading="lazy">
          </div>
          <p>Welcome! I'm your iStart assistant. How can I help you today?</p>
        </div>
      </div>
      
      <!-- Typing Indicator -->
      <div class="typing-indicator" id="typingIndicator" aria-live="polite">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <span>iStart Assistant is typing...</span>
      </div>
    </div>

    <!-- Input Area (Fixed at Bottom) -->
    <footer class="input-area">
      <input type="text" id="messageInput" placeholder="Type your question here..." aria-label="Type your question">
      <button class="send-btn" onclick="handleSend()" aria-label="Send message">
        <i class="fas fa-paper-plane"></i>
        <div class="send-hover"></div>
      </button>
    </footer>
  </section>

  <!-- Load JavaScript with defer (assumes your chat.js handles chat functionality) -->
  <script src="{{ asset('js/chat.js') }}" defer></script>
</body>
</html>



