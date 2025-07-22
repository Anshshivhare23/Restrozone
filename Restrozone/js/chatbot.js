// Google Gemini Chatbot implementation with browser-based chat history
document.addEventListener('DOMContentLoaded', function() {
    // Create chatbot UI elements if they don't exist
    if (!document.getElementById('chatbot-container')) {
        createChatbotUI();
    }

    // Get references to DOM elements
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotClear = document.getElementById('chatbot-clear');

    // Chat history to provide context for the AI
    let chatHistory = [];
    // User ID for localStorage key (default to session ID if not logged in)
    let userId = getUserId();

    // Function to get user ID from session if available
    function getUserId() {
        // Try to get the user ID from session
        // If not available, generate a unique session ID
        try {
            const sessionUserId = document.cookie
                .split('; ')
                .find(row => row.startsWith('user_id='))
                ?.split('=')[1];
                
            if (sessionUserId) {
                return `user_${sessionUserId}`;
            }
        } catch (e) {
            console.log('No user ID found in session');
        }
        
        // Generate a session ID if no user ID found
        let sessionId = localStorage.getItem('restrozone_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
            localStorage.setItem('restrozone_session_id', sessionId);
        }
        return sessionId;
    }

    // Load chat history from localStorage
    function loadChatHistory() {
        const key = `restrozone_chat_${userId}`;
        const savedData = localStorage.getItem(key);
        
        if (savedData) {
            try {
                const parsedData = JSON.parse(savedData);
                
                // Clear current UI
                while (chatbotMessages.firstChild) {
                    chatbotMessages.removeChild(chatbotMessages.firstChild);
                }
                
                // Set chat history for API context
                chatHistory = parsedData.history || [];
                
                // Recreate UI messages
                if (parsedData.messages && parsedData.messages.length > 0) {
                    parsedData.messages.forEach(msg => {
                        const messageElement = document.createElement('div');
                        messageElement.className = `chatbot-message ${msg.sender}`;
                        if (msg.sender === 'bot') {
                            messageElement.innerHTML = msg.text;
                        } else {
                            messageElement.textContent = msg.text;
                        }
                        chatbotMessages.appendChild(messageElement);
                    });
                } else {
                    addWelcomeMessage();
                }
            } catch (e) {
                console.error('Error parsing saved chat history', e);
                addWelcomeMessage();
            }
        } else {
            addWelcomeMessage();
        }
    }
    
    // Save chat history to localStorage
    function saveChatHistory() {
        const messagesToSave = [];
        
        // Get all message elements from DOM
        const messageElements = chatbotMessages.querySelectorAll('.chatbot-message');
        messageElements.forEach(element => {
            const sender = element.classList.contains('user') ? 'user' : 'bot';
            const text = sender === 'bot' ? element.innerHTML : element.textContent;
            messagesToSave.push({ sender, text });
        });
        
        const dataToSave = {
            history: chatHistory,
            messages: messagesToSave
        };
        
        // Save to localStorage
        const key = `restrozone_chat_${userId}`;
        localStorage.setItem(key, JSON.stringify(dataToSave));
    }

    // Clear chat history
    function clearChatHistory() {
        if (confirm('Are you sure you want to clear your chat history?')) {
            // Clear the UI
            while (chatbotMessages.firstChild) {
                chatbotMessages.removeChild(chatbotMessages.firstChild);
            }
            
            // Reset history
            chatHistory = [];
            
            // Add welcome message
            addWelcomeMessage();
            
            // Clear from localStorage
            const key = `restrozone_chat_${userId}`;
            localStorage.removeItem(key);
        }
    }
    
    // Add welcome message to chat
    function addWelcomeMessage() {
        const welcome = document.createElement('div');
        welcome.className = 'chatbot-message bot';
        welcome.innerHTML = 'Hello! I\'m your Restrozone assistant. I can help with:<br>• Finding restaurants<br>• Menu information<br>• Placing orders<br>• Table bookings<br>• Account questions<br><br>How can I assist you today?';
        chatbotMessages.appendChild(welcome);
    }

    // Initial load of chat history
    loadChatHistory();

    // Handle responsive behavior
    function handleResponsiveLayout() {
        const isMobile = window.innerWidth <= 768;
        
        if (isMobile) {
            // On mobile, position the chat window in the center
            chatbotContainer.style.right = '50%';
            chatbotContainer.style.transform = chatbotContainer.classList.contains('open') 
                ? 'translateX(50%) translateY(0)' 
                : 'translateX(50%) translateY(20px)';
        } else {
            // On desktop, position the chat window on the right
            chatbotContainer.style.right = '20px';
            chatbotContainer.style.transform = chatbotContainer.classList.contains('open') 
                ? 'translateY(0)' 
                : 'translateY(20px)';
        }
    }

    // Listen for window resize events
    window.addEventListener('resize', handleResponsiveLayout);
    
    // Initial call to set up the right layout
    handleResponsiveLayout();

    // Toggle chatbot visibility
    chatbotToggle.addEventListener('click', function() {
        chatbotContainer.classList.toggle('open');
        if (chatbotContainer.classList.contains('open')) {
            chatbotInput.focus();
            handleResponsiveLayout(); // Update positioning when opening
        }
    });

    // Close chatbot
    chatbotClose.addEventListener('click', function() {
        chatbotContainer.classList.remove('open');
        handleResponsiveLayout(); // Update positioning when closing
    });

    // Clear chat history
    if (chatbotClear) {
        chatbotClear.addEventListener('click', clearChatHistory);
    }

    // Send message function
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message !== '') {
            // Add user message to chat
            addMessage('user', message);
            chatbotInput.value = '';
            
            // Show typing indicator
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'chatbot-message bot typing';
            typingIndicator.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            chatbotMessages.appendChild(typingIndicator);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            // Add to chat history
            chatHistory.push({ role: 'user', parts: [{ text: message }] });

            // Make API call to Google Gemini
            fetchGeminiResponse(message, chatHistory)
                .then(response => {
                    // Remove typing indicator
                    chatbotMessages.removeChild(typingIndicator);
                    
                    // Process and display the response
                    displayFormattedResponse(response);
                    
                    // Add to chat history
                    chatHistory.push({ role: 'model', parts: [{ text: response }] });
                    
                    // Limit history length to prevent token overflow
                    if (chatHistory.length > 10) {
                        chatHistory = chatHistory.slice(chatHistory.length - 10);
                    }
                    
                    // Save chat history
                    saveChatHistory();
                })
                .catch(error => {
                    // Remove typing indicator
                    chatbotMessages.removeChild(typingIndicator);
                    
                    // CHANGED: Display the actual error message instead of generic text
                    console.error('Gemini API Error:', error);
                    
                    // Get any saved error details from localStorage
                    const lastErrorJSON = localStorage.getItem('gemini_last_error');
                    let errorMessage = error.message || 'Unknown error occurred';
                    
                    if (lastErrorJSON) {
                        try {
                            const errorData = JSON.parse(lastErrorJSON);
                            if (errorData.error && errorData.error.message) {
                                errorMessage = `API Error: ${errorData.error.message} (Code: ${errorData.error.code || 'unknown'})`;
                            }
                        } catch (e) {
                            console.error('Error parsing saved error JSON:', e);
                        }
                    }
                    
                    // Display the actual error message to the user
                    addMessage('bot', `Technical error details: ${errorMessage}`);
                });
        }
    }

    // Display formatted response with support for links and basic formatting
    function displayFormattedResponse(response) {
        // Process for links - replace URLs with anchor tags
        response = response.replace(
            /(https?:\/\/[^\s]+)/g, 
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
        );
        
        // Create message element
        const messageElement = document.createElement('div');
        messageElement.className = 'chatbot-message bot';
        messageElement.innerHTML = response;
        
        chatbotMessages.appendChild(messageElement);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Add plain text message to chat
    function addMessage(sender, message) {
        const messageElement = document.createElement('div');
        messageElement.className = `chatbot-message ${sender}`;
        messageElement.textContent = message;
        chatbotMessages.appendChild(messageElement);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Handle send button click
    chatbotSend.addEventListener('click', sendMessage);

    // Handle enter key press
    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // API call to Google Gemini
    async function fetchGeminiResponse(message, history) {
        // Your Google Gemini API key - replace with your actual key from https://aistudio.google.com/apikey
        const GEMINI_API_KEY = 'AIzaSyDqQqvslqoAWuwIRuSd7nS0KlPbw2lw-qU'; // Replace with your actual API key
        
        // FIXED: Removed the incorrect condition that was causing the error
        // Only check if API key is empty or placeholder
        if (!GEMINI_API_KEY || GEMINI_API_KEY === 'YOUR_API_KEY_HERE') {
            console.error('Gemini API key not set. Please replace the placeholder with your actual API key.');
            // Return error object instead of string
            throw new Error('API key not configured');
        }
        
        // Updated API URL to use gemini-2.0-flash model
        const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        
        // Enhanced rate limiting using localStorage
        const rateLimitKey = 'gemini_last_request_time';
        const rateLimitCountKey = 'gemini_request_count';
        const cooldownKey = 'gemini_in_cooldown';
        
        const lastRequestTime = localStorage.getItem(rateLimitKey);
        const requestCount = parseInt(localStorage.getItem(rateLimitCountKey) || '0');
        const inCooldown = localStorage.getItem(cooldownKey) === 'true';
        const currentTime = Date.now();
        
        // Check if we're in cooldown mode
        if (inCooldown) {
            const cooldownEndTime = parseInt(localStorage.getItem('gemini_cooldown_end') || '0');
            if (currentTime < cooldownEndTime) {
                const secondsLeft = Math.ceil((cooldownEndTime - currentTime) / 1000);
                return `I need a brief moment to catch up. Please try again in ${secondsLeft} seconds.`;
            } else {
                // Cooldown period over
                localStorage.setItem(cooldownKey, 'false');
                localStorage.setItem(rateLimitCountKey, '0');
            }
        }
        
        // Normal rate limiting - minimum 3 seconds between requests
        const minRequestInterval = 3000;
        if (lastRequestTime && (currentTime - parseInt(lastRequestTime)) < minRequestInterval) {
            return "Please wait a moment before sending another message.";
        }
        
        // Track this request
        localStorage.setItem(rateLimitKey, currentTime.toString());
        localStorage.setItem(rateLimitCountKey, (requestCount + 1).toString());
        
        // If we've made too many requests recently, enter cooldown mode
        if (requestCount >= 5) {
            const cooldownPeriod = 60000; // 60 seconds cooldown
            localStorage.setItem(cooldownKey, 'true');
            localStorage.setItem('gemini_cooldown_end', (currentTime + cooldownPeriod).toString());
            localStorage.setItem(rateLimitCountKey, '0');
        }
        
        // System prompt with site knowledge
        const systemPrompt = `You are a helpful and professional assistant for Restrozone, a food delivery and restaurant booking website.

        Primary Tasks - When a user asks a question, focus on these five core tasks:
        1. Finding restaurants - Help users discover restaurants by cuisine type, dish, or location
        2. Menu information - Provide detailed dish descriptions, prices, and ingredients
        3. Placing orders - Guide users through the ordering process
        4. Table bookings - Assist with reservation requests
        5. Account questions - Help with login, registration, profile management

        Website Knowledge:
        - Restrozone lets users browse restaurants, order food online, and book tables.
        - Users can create accounts, login, and track their orders.
        - The site features various cuisines and restaurants with detailed menus.
        - Payment options include Paypal, Mastercard, Maestro, Stripe, and Bitcoin.
        - Users can leave feedback after their dining experience.
        - For customer service inquiries, the phone number is +918792132884.
        - The restaurant is located in BIT Wardha, Maharashtra, India.
        
        Restaurants and their specialties:
        1. Chinese Restaurant (Restaurant ID: 1)
           - Known for: Noodles, Manchurian, Dumplings
           - Signature dish: Noodles with vegetables stir-fry
           - Price range: Rs.60-70
           - Slogen: "The ultimate mood-lifter – Chinese food never disappoints!"
        
        2. South Indian Restaurant (Restaurant ID: 2)
           - Known for: Dosa, Idli, Mendu wada, Biryani
           - Signature dish: Crispy Dosa with sambar and chutney
           - Price range: Rs.40-110
           - Slogen: "The crunch of dosa, the punch of chutney – unbeatable!"
        
        3. North Indian Restaurant (Restaurant ID: 3)
           - Known for: Aloo paratha, Choole Bhatura, Chicken, Fish curry
           - Signature dish: Butter Chicken with naan
           - Price range: Rs.50-200
           - Slogen: "From street chaat to royal thalis – North India has it all!"
        
        4. Maharashtra Restaurant (Restaurant ID: 4)
           - Known for: Misal Pav, Vada pav, Batata Vada, Poha
           - Signature dish: Spicy Misal Pav
           - Price range: Rs.20-35
           - Slogen: "Authentic Maharashtrian flavors that hit the soul!"
        
        Menu details:
        - Noodles (Rs.60): Available at Chinese Restaurant. A flavorful stir-fry of soft, slurpy noodles tossed with crunchy vegetables, aromatic garlic, and soy sauce.
        - Manchurian (Rs.60): Available at Chinese Restaurant. A popular Indo-Chinese dish featuring crispy fried vegetable or chicken balls soaked in a tangy, spicy Manchurian sauce.
        - Dumplings (Rs.70): Available at Chinese Restaurant. Steamed or fried parcels filled with finely chopped vegetables, chicken, or paneer, seasoned with Asian herbs and spices.
        - Dosa (Rs.40): Available at South Indian Restaurant. A thin, crispy crepe made from a fermented rice and lentil batter, golden brown and served hot.
        - Idli (Rs.40): Available at South Indian Restaurant. Soft, fluffy, and steamed to perfection, a staple South Indian breakfast.
        - Mendu wada (Rs.80): Available at South Indian Restaurant. Crispy on the outside, soft and airy inside, savory lentil doughnuts made from urad dal.
        - Biryani (Rs.110): Available at South Indian Restaurant. Aromatic and richly spiced, blends fragrant basmati rice with marinated meat.
        - Aloo paratha (Rs.50): Available at North Indian Restaurant. Stuffed potato flatbread served with curd or pickle.
        - Choole Bhatura (Rs.70): Available at North Indian Restaurant. Spicy chickpeas served with deep-fried bread.
        - Chicken (Rs.200): Available at North Indian Restaurant. Tandoori chicken in a creamy tomato gravy.
        - Fish curry (Rs.180): Available at North Indian Restaurant. Tender fish pieces simmered in a spiced, tangy gravy of onions, tomatoes, and traditional Indian spices.
        - Misal Pav (Rs.35): Available at Maharashtra Restaurant. A spicy sprouted moth bean curry topped with crunchy farsan, chopped onions, and coriander.
        - Vada pav (Rs.20): Available at Maharashtra Restaurant. Served with traditional spicy queso and marinara sauce.
        - Batata Vada (Rs.30): Available at Maharashtra Restaurant. Mashed potatoes seasoned with mustard seeds, garlic, ginger, and green chilies.
        - Poha (Rs.35): Available at Maharashtra Restaurant. Flattened rice lightly tempered with mustard seeds, turmeric, curry leaves, and green chilies.
        
        Task-Specific Response Guidelines:
        
        1. Restaurant Queries:
           - If asked "Which restaurants do you have?", list all 4 restaurants with their cuisine types
           - If asked about cuisine types, mention we have Chinese, South Indian, North Indian, and Maharashtrian
           - If asked for recommendations, suggest based on popular dishes or price range
           - Example: "We have 4 restaurants: Chinese, South Indian, North Indian, and Maharashtra. Each specializes in authentic cuisine from their region."

        2. Menu Information:
           - If asked about a specific dish (e.g., "Do you have noodles?"), confirm which restaurant has it, price, and description
           - If asked "What's on the menu at [restaurant]?", list their signature dishes
           - For dish recommendations, suggest signature dishes from each restaurant
           - Example: "Yes, we serve Noodles at our Chinese Restaurant for Rs.60. It's a flavorful stir-fry with vegetables, garlic, and soy sauce."

        3. Order Placement:
           - If asked how to order, explain the online ordering process
           - For questions about delivery, mention delivery options and times
           - For payment queries, list our payment methods
           - Example: "To place an order, browse our restaurants, select your dishes, add them to cart, and proceed to checkout. We accept various payment methods including cards and Bitcoin."

        4. Table Bookings:
           - If asked about reservations, explain how to book a table
           - For availability questions, guide them to check the booking calendar
           - If they want to make a reservation, ask for date, time, number of people
           - Example: "You can book a table through our website by selecting the restaurant, date, time, and number of guests. Would you like me to help you make a reservation now?"

        5. Account Questions:
           - For login issues, provide steps to recover password
           - For registration questions, explain the signup process
           - For profile management, explain how to edit profile details
           - Example: "To create an account, click the 'Register' button, fill in your details, and submit the form. You'll need to provide your name, email, and create a password."
        
        Always be friendly, concise, and helpful. Focus on answering the user's question directly first, then offer additional relevant information. For questions you don't know the answer to, politely suggest the user contact customer service at +918792132884.`;
        
        try {
            console.log('Attempting API call to Google Gemini...');
            
            // Format the conversation for the new API format
            // The new API expects a simpler format with just "contents" array
            const contents = [];
            
            // Add the user's current message
            contents.push({
                parts: [{ text: message }]
            });
            
            // Simplified request body structure for gemini-2.0-flash
            const requestBody = {
                contents: contents,
                generationConfig: {
                    temperature: 0.7,
                    maxOutputTokens: 800,
                    topP: 0.95,
                    topK: 40
                }
            };
            
            console.log('Using API URL:', API_URL);
            
            // API call with proper format
            const response = await fetch(`${API_URL}?key=${GEMINI_API_KEY}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });
            
            console.log('API response status:', response.status);
            
            // Get response data regardless of success status to log it
            let responseData;
            try {
                responseData = await response.clone().json();
                console.log('Full API response:', JSON.stringify(responseData, null, 2));
            } catch (jsonError) {
                console.error('Error parsing response JSON:', jsonError);
                const responseText = await response.clone().text();
                console.log('Raw response text:', responseText);
            }
            
            if (!response.ok) {
                let errorInfo = '';
                let errorData;
                
                try {
                    errorData = await response.json();
                    console.error('API Error Details:', JSON.stringify(errorData, null, 2));
                    
                    // Save the full error JSON to localStorage for debugging
                    localStorage.setItem('gemini_last_error', JSON.stringify(errorData));
                    
                    // FIXED: Return error object directly instead of string
                    if (errorData.error) {
                        // Throw the actual error object from the API
                        throw errorData;
                    }
                } catch (parseError) {
                    if (errorData) {
                        // If we have error data, throw that
                        throw errorData;
                    } else {
                        // Otherwise create a basic error object
                        throw {
                            error: {
                                code: response.status,
                                message: `Status: ${response.status}, ${response.statusText}`,
                                status: response.statusText
                            }
                        };
                    }
                }
                
                // If we reach here, create a generic error for different status codes
                if (response.status === 401) {
                    throw {
                        error: {
                            code: 401,
                            message: 'Authentication failed. Invalid API key.',
                            status: 'UNAUTHENTICATED'
                        }
                    };
                } else if (response.status === 429) {
                    // API Rate limit exceeded - enter cooldown mode
                    const cooldownPeriod = 120000; // 2 minutes cooldown
                    localStorage.setItem(cooldownKey, 'true');
                    localStorage.setItem('gemini_cooldown_end', (currentTime + cooldownPeriod).toString());
                    localStorage.setItem(rateLimitCountKey, '0');
                    
                    // Throw rate limit error
                    throw {
                        error: {
                            code: 429,
                            message: 'Rate limit exceeded. Too many requests.',
                            status: 'RESOURCE_EXHAUSTED'
                        }
                    };
                } else if (response.status === 404) {
                    throw {
                        error: {
                            code: 404,
                            message: 'The requested API endpoint was not found.',
                            status: 'NOT_FOUND'
                        }
                    };
                } else {
                    // Include error info in the thrown error
                    throw {
                        error: {
                            code: response.status,
                            message: errorInfo || 'Unknown API error',
                            status: response.statusText
                        }
                    };
                }
            }
            
            const data = await response.json();
            console.log('API response received');
            
            // Parse the response based on the new API format
            if (data && data.candidates && data.candidates.length > 0 && 
                data.candidates[0].content && data.candidates[0].content.parts && 
                data.candidates[0].content.parts.length > 0) {
                
                // Reset request count after successful response
                if (requestCount > 1) {
                    localStorage.setItem(rateLimitCountKey, '1');
                }
                
                return data.candidates[0].content.parts[0].text;
            } else {
                console.error('Unexpected response structure:', data);
                throw new Error('Received invalid response structure from API');
            }
            
        } catch (error) {
            console.error('Error calling Google Gemini API:', error);
            
            // FIXED: Return the error object directly
            // If error already has the right structure, return it
            if (error.error && typeof error.error === 'object') {
                throw error;
            }
            
            // Otherwise, create proper error structure
            throw {
                error: {
                    code: error.code || 500,
                    message: error.message || 'Unknown error occurred',
                    status: error.name || 'INTERNAL'
                }
            };
        }
    }

    // Create chatbot UI elements
    function createChatbotUI() {
        // Create toggle button
        const toggle = document.createElement('div');
        toggle.id = 'chatbot-toggle';
        toggle.innerHTML = '<i class="fa fa-comments"></i>';
        
        // Create chatbot container
        const container = document.createElement('div');
        container.id = 'chatbot-container';
        
        // Create chatbot header
        const header = document.createElement('div');
        header.id = 'chatbot-header';
        header.innerHTML = '<h3>Restrozone Assistant</h3><div class="chatbot-controls"><button id="chatbot-clear" title="Clear History"><i class="fa fa-trash"></i></button><button id="chatbot-close"><i class="fa fa-times"></i></button></div>';
        
        // Create chatbot messages container
        const messages = document.createElement('div');
        messages.id = 'chatbot-messages';
        
        // Create chatbot input area
        const inputArea = document.createElement('div');
        inputArea.id = 'chatbot-input-area';
        inputArea.innerHTML = '<input type="text" id="chatbot-input" placeholder="Type your message..."><button id="chatbot-send"><i class="fa fa-paper-plane"></i></button>';
        
        // Assemble the chatbot
        container.appendChild(header);
        container.appendChild(messages);
        container.appendChild(inputArea);
        
        // Add to the document
        document.body.appendChild(toggle);
        document.body.appendChild(container);
    }
});
