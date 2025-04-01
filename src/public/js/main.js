document.addEventListener('DOMContentLoaded', function() {
    // Додаємо анімацію для заголовка
    const header = document.querySelector('.header h1');
    if (header) {
        header.style.opacity = '0';
        setTimeout(() => {
            header.style.transition = 'opacity 1s ease-in-out';
            header.style.opacity = '1';
        }, 100);
    }

    // Додаємо анімацію для кнопки
    const button = document.querySelector('.button');
    if (button) {
        button.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.05)';
        });
        button.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    }
}); 