document.addEventListener('DOMContentLoaded', () => {
    const submitPromptBtn = document.querySelector('#submit-prompt');
    const userPromptInput = document.querySelector('#user-prompt');
    const responseMessage = document.querySelector('#response-message');
    const loadingSpinner = document.querySelector('#loading-spinner');

    const submitHandler = () => {
        const userPrompt = userPromptInput.value.trim();
        if (!userPrompt) {
            alert('Please enter a topic.');
            return;
        }

        loadingSpinner.classList.add('active');
        responseMessage.innerHTML = '';

        const formData = new FormData();
        formData.append('action', 'generate_post');
        formData.append('nonce', post_generator.nonce);
        formData.append('prompt', userPrompt);

        fetch(post_generator.ajax_url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.remove('active');
                if (data.success) {
                    responseMessage.innerHTML = `<div class="updated notice">${data.data.message}</div>`;
                } else {
                    responseMessage.innerHTML = `<div class="error notice">${data.data.message}</div>`;
                }
            })
            .catch(error => {
                loadingSpinner.classList.remove('active');
                responseMessage.innerHTML = `<div class="error notice">Error</div>`;
            });
    };

    submitPromptBtn.addEventListener('click', submitHandler);

    userPromptInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            submitHandler();
        }
    });
});
