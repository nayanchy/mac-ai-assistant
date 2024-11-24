<div class="mac-ai-form-container">
    <h2>Mac AI Assistant</h2>
    <form id="mac-ai-form">
        <div id="mac-ai-question-container">
            <label id="mac-ai-question-label"></label>
            <input type="text" id="mac-ai-answer-input" placeholder="Type your answer here" />
            <select id="mac-ai-answer-options" style="display: none;"></select>
        </div>

        <div class="mac-ai-buttons">
            <button type="button" id="mac-ai-submit-answer">Submit My Answer</button>
            <button type="button" id="mac-ai-generate-answer">Generate with AI</button>
        </div>

        <div class="mac-ai-loading">
            <img src="<?php echo plugin_dir_url(__FILE__) . '../assets/images/loader.gif'; ?>" alt="Loading">
        </div>
    </form>
</div>