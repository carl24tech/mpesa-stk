document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('stkForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const spinner = submitBtn.querySelector('.spinner');
    const messageBox = document.getElementById('message-box');
    const phoneInput = document.getElementById('phone');
    
    // Phone number formatting
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 9) {
            value = value.substr(0, 9);
        }
        e.target.value = value;
    });
    
    // Form submission
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Hide previous messages
        messageBox.classList.add('hidden');
        messageBox.className = 'message-box hidden';
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Disable button and show spinner
        setLoading(true);
        
        // Prepare form data
        const formData = new FormData(form);
        
        try {
            const response = await fetch('process.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showMessage('success', data.message);
                form.reset();
            } else {
                showMessage('error', data.message);
            }
        } catch (error) {
            showMessage('error', 'Network error. Please check your connection and try again.');
            console.error('Error:', error);
        } finally {
            setLoading(false);
        }
    });
    
    // Validate form fields
    function validateForm() {
        const phone = phoneInput.value.trim();
        const amount = document.getElementById('amount').value;
        
        // Validate phone number
        if (!phone || phone.length !== 9) {
            showMessage('error', 'Please enter a valid 9-digit phone number');
            phoneInput.focus();
            return false;
        }
        
        // Check if phone starts with valid prefix
        const validPrefixes = ['7', '1'];
        if (!validPrefixes.includes(phone.charAt(0))) {
            showMessage('error', 'Phone number must start with 7 or 1');
            phoneInput.focus();
            return false;
        }
        
        // Validate amount
        if (!amount || amount < 1 || amount > 150000) {
            showMessage('error', 'Amount must be between 1 and 150,000 KES');
            document.getElementById('amount').focus();
            return false;
        }
        
        return true;
    }
    
    // Show message
    function showMessage(type, text) {
        messageBox.textContent = text;
        messageBox.className = `message-box ${type}`;
        messageBox.classList.remove('hidden');
        
        // Auto-hide after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(() => {
                messageBox.classList.add('hidden');
            }, 5000);
        }
    }
    
    // Toggle loading state
    function setLoading(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            btnText.classList.add('hidden');
            spinner.classList.remove('hidden');
        } else {
            btnText.classList.remove('hidden');
            spinner.classList.add('hidden');
        }
    }
    
    // Amount input validation
    const amountInput = document.getElementById('amount');
    amountInput.addEventListener('input', function(e) {
        let value = parseInt(e.target.value);
        if (value > 150000) {
            e.target.value = 150000;
            showMessage('info', 'Maximum amount is 150,000 KES');
        }
        if (value < 1 && e.target.value !== '') {
            e.target.value = 1;
        }
    });
});
