<?php
/**
 * AI Assistant Chat Widget
 * Floating chat interface for AI assistance
 */

// Check if AI is enabled
if (!is_ai_enabled()) {
    return;
}

$can_access = $ai_access['allowed'] ?? false;
$checkout_url = $ai_access['checkout_url'] ?? null;
$access_reason = $ai_access['reason'] ?? null;
?>

<div id="js-ai-assistant-wrapper" class="ai-assistant-wrapper hide">
    <?php if ($can_access): ?>
        <!-- AI Chat Interface -->
        <div class="ai-chat-container">
            <div class="ai-chat-header">
                <div class="d-flex align-items-center">
                    <span class="ai-chat-title">
                        <i data-feather="cpu" class="icon-16 me-2"></i>
                        <?php echo app_lang('ai_assistant'); ?>
                    </span>
                </div>
                <div class="ai-chat-actions">
                    <button type="button" class="btn btn-link p-1 ai-clear-chat" title="<?php echo app_lang('ai_new_chat'); ?>">
                        <i data-feather="refresh-cw" class="icon-16"></i>
                    </button>
                    <button type="button" class="btn btn-link p-1 ai-close-chat" title="<?php echo app_lang('close'); ?>">
                        <i data-feather="x" class="icon-16"></i>
                    </button>
                </div>
            </div>

            <div class="ai-chat-body" id="ai-chat-messages">
                <div class="ai-welcome-message">
                    <div class="ai-message ai-message-assistant">
                        <div class="ai-message-content">
                            <?php echo app_lang('ai_welcome_message'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ai-chat-footer">
                <form id="ai-chat-form" class="ai-chat-input-form">
                    <div class="input-group">
                        <textarea
                            id="ai-chat-input"
                            class="form-control"
                            placeholder="<?php echo app_lang('ai_input_placeholder'); ?>"
                            rows="1"
                            maxlength="2000"
                        ></textarea>
                        <button type="submit" class="btn btn-primary" id="ai-send-btn">
                            <i data-feather="send" class="icon-16"></i>
                        </button>
                    </div>
                    <div class="ai-input-info">
                        <small class="text-muted"><?php echo app_lang('ai_input_hint'); ?></small>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Subscription Required Message -->
        <div class="ai-chat-container">
            <div class="ai-chat-header">
                <div class="d-flex align-items-center">
                    <span class="ai-chat-title">
                        <i data-feather="cpu" class="icon-16 me-2"></i>
                        <?php echo app_lang('ai_assistant'); ?>
                    </span>
                </div>
                <div class="ai-chat-actions">
                    <button type="button" class="btn btn-link p-1 ai-close-chat" title="<?php echo app_lang('close'); ?>">
                        <i data-feather="x" class="icon-16"></i>
                    </button>
                </div>
            </div>

            <div class="ai-chat-body ai-subscription-required">
                <div class="text-center p-4">
                    <div class="mb-3">
                        <i data-feather="lock" class="icon-48 text-muted"></i>
                    </div>
                    <h5><?php echo app_lang('ai_subscription_required_title'); ?></h5>
                    <p class="text-muted mb-4"><?php echo app_lang('ai_subscription_required_message'); ?></p>
                    <?php if ($checkout_url): ?>
                        <a href="<?php echo $checkout_url; ?>" class="btn btn-primary" target="_blank">
                            <i data-feather="credit-card" class="icon-16 me-1"></i>
                            <?php echo app_lang('ai_subscribe_now'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var aiSessionId = null;
    var isProcessing = false;

    // Create AI Assistant floating icon
    var $aiIconWrapper = $('<div id="js-ai-assistant-icon" class="ai-assistant-icon"></div>');
    $aiIconWrapper.append('<span id="js-ai-icon" data-type="open" class="ai-icon-btn"><i data-feather="cpu" class="icon"></i></span>');

    // Append to body
    if (isMobile()) {
        // For mobile, could add to mobile menu - for now add to body
        $('body').append($aiIconWrapper);
    } else {
        $('body').append($aiIconWrapper);
    }

    // Toggle AI chat window
    $('#js-ai-assistant-icon').click(function() {
        var $icon = $('#js-ai-icon');
        var $wrapper = $('#js-ai-assistant-wrapper');

        if ($icon.attr('data-type') === 'open') {
            $wrapper.removeClass('hide');
            $icon.attr('data-type', 'close').html('<i data-feather="chevron-down" class="icon-22"></i>');
            $('#ai-chat-input').focus();
        } else {
            $wrapper.addClass('hide');
            $icon.attr('data-type', 'open').html('<i data-feather="cpu" class="icon"></i>');
        }
        feather.replace();
    });

    // Close AI chat
    $(document).on('click', '.ai-close-chat', function() {
        $('#js-ai-assistant-wrapper').addClass('hide');
        $('#js-ai-icon').attr('data-type', 'open').html('<i data-feather="cpu" class="icon"></i>');
        feather.replace();
    });

    // Clear chat / New conversation
    $(document).on('click', '.ai-clear-chat', function() {
        aiSessionId = null;
        $('#ai-chat-messages').html(`
            <div class="ai-welcome-message">
                <div class="ai-message ai-message-assistant">
                    <div class="ai-message-content">
                        <?php echo addslashes(app_lang('ai_welcome_message')); ?>
                    </div>
                </div>
            </div>
        `);

        // Clear session on server
        $.post('<?php echo get_uri("ai_assistant/clear_session"); ?>', {
            session_id: aiSessionId,
            <?php echo csrf_token(); ?>: '<?php echo csrf_hash(); ?>'
        });
    });

    // Auto-resize textarea
    $('#ai-chat-input').on('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Handle Enter key (Shift+Enter for new line)
    $('#ai-chat-input').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#ai-chat-form').submit();
        }
    });

    // Submit AI query
    $('#ai-chat-form').on('submit', function(e) {
        e.preventDefault();

        if (isProcessing) return;

        var query = $('#ai-chat-input').val().trim();
        if (!query) return;

        isProcessing = true;
        $('#ai-send-btn').prop('disabled', true);

        // Add user message to chat
        appendMessage('user', query);

        // Clear input
        $('#ai-chat-input').val('').css('height', 'auto');

        // Show typing indicator
        showTypingIndicator();

        // Send to server
        $.ajax({
            url: '<?php echo get_uri("ai_assistant/query"); ?>',
            type: 'POST',
            data: {
                query: query,
                session_id: aiSessionId,
                <?php echo csrf_token(); ?>: '<?php echo csrf_hash(); ?>'
            },
            success: function(response) {
                hideTypingIndicator();

                if (response.success) {
                    aiSessionId = response.session_id;
                    appendMessage('assistant', response.response);
                } else {
                    if (response.reason === 'subscription_required' && response.checkout_url) {
                        appendMessage('system', response.message + ' <a href="' + response.checkout_url + '" target="_blank" class="btn btn-sm btn-primary mt-2"><?php echo app_lang("ai_subscribe_now"); ?></a>');
                    } else {
                        appendMessage('error', response.message || '<?php echo app_lang("ai_error_occurred"); ?>');
                    }
                }
            },
            error: function() {
                hideTypingIndicator();
                appendMessage('error', '<?php echo app_lang("ai_connection_error"); ?>');
            },
            complete: function() {
                isProcessing = false;
                $('#ai-send-btn').prop('disabled', false);
            }
        });
    });

    function appendMessage(type, content) {
        var messageClass = 'ai-message-' + type;
        var $message = $('<div class="ai-message ' + messageClass + '"><div class="ai-message-content"></div></div>');

        if (type === 'assistant') {
            // Parse markdown-like formatting for AI responses
            content = formatAIResponse(content);
        }

        $message.find('.ai-message-content').html(content);
        $('#ai-chat-messages').append($message);
        scrollToBottom();
    }

    function showTypingIndicator() {
        var $typing = $('<div class="ai-message ai-message-assistant ai-typing-indicator"><div class="ai-message-content"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div></div>');
        $('#ai-chat-messages').append($typing);
        scrollToBottom();
    }

    function hideTypingIndicator() {
        $('.ai-typing-indicator').remove();
    }

    function scrollToBottom() {
        var $chatBody = $('#ai-chat-messages');
        $chatBody.scrollTop($chatBody[0].scrollHeight);
    }

    function formatAIResponse(text) {
        // Basic markdown-like formatting
        // Code blocks
        text = text.replace(/```(\w+)?\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>');
        // Inline code
        text = text.replace(/`([^`]+)`/g, '<code>$1</code>');
        // Bold
        text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        // Italic
        text = text.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        // Line breaks
        text = text.replace(/\n/g, '<br>');
        return text;
    }

    // Initial feather icons
    setTimeout(function() {
        feather.replace();
    }, 100);
});
</script>

<style>
/* AI Assistant Icon */
.ai-assistant-icon {
    position: fixed;
    bottom: 80px;
    right: 20px;
    z-index: 1050;
}

.ai-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
}

.ai-icon-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

/* AI Assistant Wrapper */
.ai-assistant-wrapper {
    position: fixed;
    bottom: 140px;
    right: 20px;
    width: 380px;
    max-height: 500px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
    z-index: 1049;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.ai-assistant-wrapper.hide {
    display: none;
}

/* AI Chat Header */
.ai-chat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.ai-chat-title {
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.ai-chat-actions .btn-link {
    color: rgba(255, 255, 255, 0.8);
    padding: 4px;
}

.ai-chat-actions .btn-link:hover {
    color: #fff;
}

/* AI Chat Body */
.ai-chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    min-height: 300px;
    max-height: 350px;
    background: #f8f9fa;
}

/* AI Messages */
.ai-message {
    margin-bottom: 12px;
    display: flex;
}

.ai-message-content {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.5;
    word-wrap: break-word;
}

.ai-message-user {
    justify-content: flex-end;
}

.ai-message-user .ai-message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    border-bottom-right-radius: 4px;
}

.ai-message-assistant .ai-message-content {
    background: #fff;
    color: #333;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.ai-message-error .ai-message-content {
    background: #fee2e2;
    color: #dc2626;
    border-left: 3px solid #dc2626;
}

.ai-message-system .ai-message-content {
    background: #fef3c7;
    color: #92400e;
    border-left: 3px solid #f59e0b;
}

/* Code formatting in messages */
.ai-message-content code {
    background: rgba(0, 0, 0, 0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
}

.ai-message-content pre {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 12px;
    border-radius: 6px;
    overflow-x: auto;
    margin: 8px 0;
}

.ai-message-content pre code {
    background: none;
    padding: 0;
    color: inherit;
}

/* Typing Indicator */
.ai-typing-indicator .ai-message-content {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 18px;
}

.ai-typing-indicator .dot {
    width: 8px;
    height: 8px;
    background: #667eea;
    border-radius: 50%;
    animation: typing-bounce 1.4s infinite ease-in-out;
}

.ai-typing-indicator .dot:nth-child(1) { animation-delay: 0s; }
.ai-typing-indicator .dot:nth-child(2) { animation-delay: 0.2s; }
.ai-typing-indicator .dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing-bounce {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

/* AI Chat Footer */
.ai-chat-footer {
    padding: 12px 16px;
    background: #fff;
    border-top: 1px solid #e5e7eb;
}

.ai-chat-input-form .input-group {
    display: flex;
    gap: 8px;
}

#ai-chat-input {
    flex: 1;
    resize: none;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 10px 12px;
    font-size: 13px;
    max-height: 120px;
    transition: border-color 0.2s;
}

#ai-chat-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

#ai-send-btn {
    border-radius: 8px;
    padding: 10px 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

#ai-send-btn:hover {
    opacity: 0.9;
}

#ai-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ai-input-info {
    margin-top: 6px;
    font-size: 11px;
}

/* Subscription Required */
.ai-subscription-required {
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-subscription-required .icon-48 {
    width: 48px;
    height: 48px;
}

/* Mobile Responsive */
@media (max-width: 576px) {
    .ai-assistant-wrapper {
        width: calc(100% - 40px);
        right: 20px;
        left: 20px;
        bottom: 80px;
        max-height: 60vh;
    }

    .ai-assistant-icon {
        bottom: 20px;
    }
}

/* RTL Support */
[dir="rtl"] .ai-assistant-icon {
    right: auto;
    left: 20px;
}

[dir="rtl"] .ai-assistant-wrapper {
    right: auto;
    left: 20px;
}

[dir="rtl"] .ai-message-user .ai-message-content {
    border-bottom-right-radius: 12px;
    border-bottom-left-radius: 4px;
}

[dir="rtl"] .ai-message-assistant .ai-message-content {
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 4px;
}
</style>
