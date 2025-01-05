document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanes = document.querySelectorAll('.tab-pane');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-target');

            tabButtons.forEach(btn => btn.classList.remove('active'));
                    
            button.classList.add('active');

            tabPanes.forEach(pane => {
                pane.classList.remove('active');
            });

            document.getElementById(target).classList.add('active');
        });
    });
    if (tabButtons.length > 0) {
        tabButtons[0].click();
    }
});