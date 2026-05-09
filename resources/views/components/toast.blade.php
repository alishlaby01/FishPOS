{{-- Toast Notification Component --}}
<div id="toast-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-2">
    <!-- Toasts will be inserted here -->
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('toast', (data) => {
        showToast(data.message, data.type);
    });
});

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-black',
        info: 'bg-blue-500 text-white'
    };

    toast.className = `neumorphic-lift ${colors[type]} px-6 py-4 rounded-2xl shadow-lg transform translate-y-[-100%] opacity-0 transition-all duration-300 max-w-md`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined">${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}</span>
            <span class="font-bold">${message}</span>
        </div>
    `;

    container.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-y-[-100%]', 'opacity-0');
    }, 10);

    // Auto remove after 4 seconds
    setTimeout(() => {
        toast.classList.add('translate-y-[-100%]', 'opacity-0');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}
</script>