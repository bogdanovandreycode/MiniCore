document.addEventListener('DOMContentLoaded', () => {
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(fileInput => {
        const fileContainer = fileInput.closest('.mb-3');
        if (!fileContainer) return;

        const fileName = fileContainer.querySelector('.file-name');
        const progressBar = fileContainer.querySelector('.progress');
        const progress = fileContainer.querySelector('.progress-bar');
        const successMessage = fileContainer.querySelector('.text-success');
        const errorMessage = fileContainer.querySelector('.text-danger');

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileName.textContent = fileInput.files[0].name;
                progressBar.style.display = 'block';
                progress.style.width = '0%';
                
                setTimeout(() => {
                    progress.style.width = '100%';
                    setTimeout(() => {
                        progressBar.style.display = 'none';
                        successMessage.style.display = 'inline';
                        errorMessage.style.display = 'none';
                    }, 500);
                }, 2000);
            } else {
                fileName.textContent = 'No file chosen';
                successMessage.style.display = 'none';
                errorMessage.style.display = 'inline';
            }
        });
    });

    const avatarFields = document.querySelectorAll('.avatar-container');

    avatarFields.forEach(field => {
        const fileInput = field.querySelector('input[type="file"]');
        const previewImage = field.querySelector('img');

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        previewImage.addEventListener('click', () => {
            fileInput.click();
        });
    });

    const dateInputs = document.querySelectorAll(".date-input");

    dateInputs.forEach(input => {
        flatpickr(input, {
            enableTime: false,
            dateFormat: "Y-m-d",
        });
    });

    const timeFields = document.querySelectorAll(".time-field");

    timeFields.forEach(field => {
        field.addEventListener("click", (e) => {
            if (e.target.matches(".increment, .decrement")) {
                const button = e.target;
                const input = button.parentNode.querySelector("input");
                const max = parseInt(input.getAttribute("max"), 10);
                const min = parseInt(input.getAttribute("min"), 10);
                const interval = parseInt(field.getAttribute("data-interval") || "1", 10);

                let value = parseInt(input.value || "0", 10);
                if (button.classList.contains("increment")) {
                    value += interval;
                } else if (button.classList.contains("decrement")) {
                    value -= interval;
                }

                if (value > max) value = min;
                if (value < min) value = max;

                input.value = value;
            }
        });
    });

    const codeFields = document.querySelectorAll('.code-field');

    codeFields.forEach(field => {
        const inputs = field.querySelectorAll('.code-input');

        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length > 0 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            input.addEventListener('input', () => {
                const allowOnlyDigits = field.getAttribute('data-allow-digits') === 'true';
                if (allowOnlyDigits && /\D/.test(input.value)) {
                    input.value = input.value.replace(/\D/g, '');
                }
            });
        });
    });

    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(alert => {
        alert.addEventListener('animationend', (event) => {
            if (event.animationName === 'fadeOut') {
                alert.remove();
            }
        });
    });
});
