<footer class="footer">
          <div class="container">


              <div class="bottom-footer">
                  <div class="row">
                      <div class="col-xs-12 col-sm-3 payment-options color-gray">
                          <h5>Payment Options</h5>
                          <ul>
                              <li>
                                  <a href="#"> <img src="images/paypal.png" alt="Paypal"> </a>
                              </li>
                              <li>
                                  <a href="#"> <img src="images/mastercard.png" alt="Mastercard"> </a>
                              </li>
                              <li>
                                  <a href="#"> <img src="images/maestro.png" alt="Maestro"> </a>
                              </li>
                              <li>
                                  <a href="#"> <img src="images/stripe.png" alt="Stripe"> </a>
                              </li>
                              <li>
                                  <a href="#"> <img src="images/bitcoin.png" alt="Bitcoin"> </a>
                              </li>
                          </ul>
                      </div>
                      <div class="col-xs-12 col-sm-4 address color-gray">
                          <h5>Address</h5>
                          <p>BIT Wardha, Maharashtra, India.</p>
                          <h5>Phone: +918792132884</a></h5>
                      </div>
                      <div class="col-xs-12 col-sm-5 additional-info color-gray">
                          <h5>Addition informations</h5>
                          <p>Join thousands of other restaurants who benefit from having partnered with us.</p>
                      </div>
                  </div>
              </div>

          </div>
      </footer>

      <!-- Chatbot Styles -->
      <style>
          #chatbot-toggle {
              position: fixed;
              bottom: 20px;
              right: 20px;
              width: 60px;
              height: 60px;
              background-color: #e74c3c;
              border-radius: 50%;
              display: flex;
              justify-content: center;
              align-items: center;
              color: white;
              cursor: pointer;
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
              z-index: 999;
              transition: all 0.3s;
          }
          
          #chatbot-toggle:hover {
              transform: scale(1.1);
          }
          
          #chatbot-toggle i {
              font-size: 24px;
          }
          
          #chatbot-container {
              position: fixed;
              bottom: 90px;
              right: 20px;
              width: 350px;
              height: 500px;
              max-width: calc(100vw - 40px);
              max-height: calc(100vh - 120px);
              background-color: white;
              border-radius: 10px;
              box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
              display: flex;
              flex-direction: column;
              overflow: hidden;
              z-index: 999;
              opacity: 0;
              pointer-events: none;
              transform: translateY(20px);
              transition: all 0.3s;
          }
          
          #chatbot-container.open {
              opacity: 1;
              pointer-events: auto;
              transform: translateY(0);
          }
          
          #chatbot-header {
              padding: 15px;
              background-color: #e74c3c;
              color: white;
              display: flex;
              justify-content: space-between;
              align-items: center;
          }
          
          #chatbot-header h3 {
              margin: 0;
              font-size: 16px;
          }
          
          .chatbot-controls {
              display: flex;
              align-items: center;
          }
          
          #chatbot-clear, #chatbot-close {
              background: none;
              border: none;
              color: white;
              cursor: pointer;
              font-size: 16px;
              margin-left: 10px;
              padding: 0;
              width: 28px;
              height: 28px;
              border-radius: 50%;
              display: flex;
              justify-content: center;
              align-items: center;
              transition: all 0.2s;
          }
          
          #chatbot-clear:hover, #chatbot-close:hover {
              background-color: rgba(255, 255, 255, 0.2);
          }
          
          #chatbot-messages {
              flex: 1;
              padding: 15px;
              overflow-y: auto;
          }
          
          .chatbot-message {
              margin-bottom: 10px;
              padding: 10px 15px;
              border-radius: 18px;
              max-width: 80%;
              word-wrap: break-word;
          }
          
          .chatbot-message.user {
              background-color: #e74c3c;
              color: white;
              align-self: flex-end;
              margin-left: auto;
          }
          
          .chatbot-message.bot {
              background-color: #f1f1f1;
              color: #333;
          }
          
          .typing-dots {
              display: flex;
              justify-content: center;
              align-items: center;
              height: 20px;
          }
          
          .typing-dots span {
              background-color: #999;
              border-radius: 50%;
              display: inline-block;
              height: 8px;
              width: 8px;
              margin: 0 2px;
              opacity: 0.6;
              animation: typing-dot 1.4s infinite ease-in-out both;
          }
          
          .typing-dots span:nth-child(1) {
              animation-delay: -0.32s;
          }
          
          .typing-dots span:nth-child(2) {
              animation-delay: -0.16s;
          }
          
          @keyframes typing-dot {
              0%, 80%, 100% { transform: scale(0.7); }
              40% { transform: scale(1); }
          }
          
          #chatbot-input-area {
              display: flex;
              padding: 10px;
              border-top: 1px solid #eee;
          }
          
          #chatbot-input {
              flex: 1;
              padding: 10px;
              border: 1px solid #ddd;
              border-radius: 20px;
              outline: none;
          }
          
          #chatbot-send {
              background: #e74c3c;
              color: white;
              border: none;
              width: 40px;
              height: 40px;
              border-radius: 50%;
              margin-left: 10px;
              cursor: pointer;
              display: flex;
              justify-content: center;
              align-items: center;
              transition: all 0.2s;
          }
          
          #chatbot-send:hover {
              background: #d63c2d;
          }
          
          #chatbot-send i {
              font-size: 16px;
          }
          
          /* Responsive styles */
          @media screen and (max-width: 768px) {
              #chatbot-container {
                  width: 85%;
                  right: 50%;
                  transform: translateX(50%) translateY(20px);
                  bottom: 80px;
              }
              
              #chatbot-container.open {
                  transform: translateX(50%) translateY(0);
              }
              
              .chatbot-message {
                  max-width: 90%;
              }
          }
          
          @media screen and (max-width: 480px) {
              #chatbot-toggle {
                  width: 50px;
                  height: 50px;
                  bottom: 15px;
                  right: 15px;
              }
              
              #chatbot-toggle i {
                  font-size: 20px;
              }
              
              #chatbot-container {
                  height: 70vh;
                  bottom: 75px;
              }
              
              #chatbot-header h3 {
                  font-size: 14px;
              }
              
              #chatbot-input {
                  padding: 8px;
              }
              
              #chatbot-send {
                  width: 36px;
                  height: 36px;
              }
          }
      </style>

      <!-- Add chatbot script to all pages -->
      <script src="js/chatbot.js"></script>

