
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.border.rounded-lg.overflow-hidden.shadow-sm');
    cards.forEach(function(card) {
        card.classList.add('card-hover-effect');
    });

    const copyButtons = document.querySelectorAll('[data-copy]');
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const textToCopy = this.getAttribute('data-copy');
            
            if (textToCopy) {
                const tempInput = document.createElement('input');
                tempInput.value = textToCopy;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }
        });
    });

    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            let hasError = false;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    hasError = true;
                    
                 
                    const errorMsg = field.parentNode.querySelector('.error-message');
                    if (!errorMsg) {
                        const msg = document.createElement('p');
                        msg.classList.add('text-red-500', 'text-sm', 'mt-1', 'error-message');
                        msg.textContent = 'This field is required';
                        field.parentNode.appendChild(msg);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    const errorMsg = field.parentNode.querySelector('.error-message');
                    if (errorMsg) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (hasError) {
                event.preventDefault();
            }
        });
    });

    const formInputs = document.querySelectorAll('input, textarea');
    formInputs.forEach(function(input) {
        input.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('border-red-500');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        });
    });

   
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(button) {
        const form = button.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="loader"></span> Processing...';
                
        
                button.setAttribute('data-original-text', originalText);
                

                setTimeout(function() {
                    if (button.disabled) {
                        button.disabled = false;
                        button.innerHTML = button.getAttribute('data-original-text');
                    }
                }, 10000);
            });
        }
    });
}); 