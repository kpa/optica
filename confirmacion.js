document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('confirmModal');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    let formToDelete = null;

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            formToDelete = button.closest('form');
            modal.style.display = 'block';
        });
    });

    confirmYes.addEventListener('click', () => {
        if (formToDelete) formToDelete.submit();
        modal.style.display = 'none';
    });

    confirmNo.addEventListener('click', () => {
        modal.style.display = 'none';
        formToDelete = null;
    });
});
