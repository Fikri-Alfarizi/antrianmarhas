<style>

    /* Modal Modern dengan Backdrop */

    .modal {

        display: none;

        position: fixed;

        z-index: 1050;

        left: 0;

        top: 0;

        width: 100%;

        height: 100%;

        background-color: rgba(0, 0, 0, 0.6);

        animation: fadeInBackdrop 0.3s ease-out;

        overflow-y: auto;

    }



    @keyframes fadeInBackdrop {

        from { background-color: rgba(0, 0, 0, 0); }

        to { background-color: rgba(0, 0, 0, 0.6); }

    }



    .modal.show {

        display: flex !important;

        align-items: center;

        justify-content: center;

    }



    .modal-content {

        background: white;

        border-radius: 12px;

        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);

        max-width: 600px;

        width: 90%;

        animation: slideUpModal 0.3s ease-out;

        overflow: hidden;

    }



    @keyframes slideUpModal {

        from {

            opacity: 0;

            transform: translateY(30px);

        }

        to {

            opacity: 1;

            transform: translateY(0);

        }

    }



    .modal-header {

        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);

        color: white;

        padding: 20px 25px;

        display: flex;

        justify-content: space-between;

        align-items: center;

        border-bottom: none;

    }



    .modal-header h3 {

        margin: 0;

        font-size: 20px;

        font-weight: 600;

        display: flex;

        align-items: center;

        gap: 10px;

    }



    .modal-header h3 i {

        font-size: 22px;

    }



    .modal-body {

        padding: 25px;

    }



    .modal-footer {

        padding: 15px 25px;

        border-top: 1px solid #eee;

        display: flex;

        justify-content: flex-end;

        gap: 10px;

        background: #f8f9fa;

    }



    .close-btn {

        color: white;

        font-size: 28px;

        font-weight: bold;

        cursor: pointer;

        line-height: 1;

        transition: all 0.2s;

        opacity: 0.8;

    }



    .close-btn:hover {

        opacity: 1;

        transform: scale(1.2);

    }



    /* Form Styling */

    .form-group {

        margin-bottom: 20px;

    }



    .form-group label {

        display: block;

        margin-bottom: 8px;

        font-weight: 600;

        color: #2c3e50;

        font-size: 14px;

    }



    .form-group input,

    .form-group select,

    .form-group textarea {

        width: 100%;

        padding: 10px 12px;

        border: 1px solid #ddd;

        border-radius: 6px;

        font-size: 14px;

        transition: all 0.3s;

        font-family: inherit;

    }



    .form-group input:focus,

    .form-group select:focus,

    .form-group textarea:focus {

        outline: none;

        border-color: #3498db;

        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);

    }



    .form-group textarea {

        resize: vertical;

        min-height: 100px;

    }



    /* Button Styles */

    .btn {

        padding: 10px 16px;

        border: none;

        border-radius: 6px;

        cursor: pointer;

        font-weight: 600;

        font-size: 14px;

        display: inline-flex;

        align-items: center;

        gap: 6px;

        transition: all 0.2s;

        font-family: inherit;

    }



    .btn:hover {

        transform: translateY(-2px);

        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    }



    .btn:active {

        transform: translateY(0);

    }



    .btn-primary {

        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);

        color: white;

    }



    .btn-primary:hover {

        background: linear-gradient(135deg, #2980b9 0%, #1f618d 100%);

    }



    .btn-secondary {

        background: #95a5a6;

        color: white;

    }



    .btn-secondary:hover {

        background: #7f8c8d;

    }



    .btn-danger {

        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);

        color: white;

    }



    .btn-danger:hover {

        background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);

    }



    .btn-edit {

        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);

        color: white;

    }



    .btn-edit:hover {

        background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);

    }



    /* Error Messages */

    .form-error {

        color: #e74c3c;

        font-size: 12px;

        margin-top: 5px;

        display: flex;

        align-items: center;

        gap: 5px;

    }



    .form-group.has-error input,

    .form-group.has-error select,

    .form-group.has-error textarea {

        border-color: #e74c3c;

        background-color: #fdf2e9;

    }



    /* Modal Overlay Click */

    .modal.modal-open {

        display: flex;

    }



    /* Responsive */

    @media (max-width: 768px) {

        .modal-content {

            width: 95%;

            max-width: 100%;

        }



        .modal-header {

            padding: 15px 20px;

        }



        .modal-header h3 {

            font-size: 18px;

        }



        .modal-body {

            padding: 20px;

        }



        .modal-footer {

            padding: 12px 20px;

            flex-wrap: wrap;

        }



        .btn {

            padding: 8px 12px;

            font-size: 13px;

        }

    }



    /* Scrollbar untuk modal yang panjang */

    .modal-body {

        max-height: calc(100vh - 200px);

        overflow-y: auto;

    }



    .modal-body::-webkit-scrollbar {

        width: 8px;

    }



    .modal-body::-webkit-scrollbar-track {

        background: #f1f1f1;

    }



    .modal-body::-webkit-scrollbar-thumb {

        background: #888;

        border-radius: 4px;

    }



    .modal-body::-webkit-scrollbar-thumb:hover {

        background: #555;

    }

</style>



<script>

    /**

     * Open Modal

     */

    function openModal(modalId) {

        const modal = document.getElementById(modalId);

        if (modal) {

            modal.style.display = 'flex';

            modal.classList.add('modal-open');

            document.body.style.overflow = 'hidden';

        }

    }



    /**

     * Close Modal

     */

    function closeModal(modalId) {

        const modal = document.getElementById(modalId);

        if (modal) {

            modal.style.display = 'none';

            modal.classList.remove('modal-open');

            document.body.style.overflow = 'auto';

        }

    }



    /**

     * Close modal ketika klik di luar modal

     */

    document.addEventListener('click', function(event) {

        if (event.target.classList.contains('modal')) {

            event.target.style.display = 'none';

            event.target.classList.remove('modal-open');

            document.body.style.overflow = 'auto';

        }

    });



    /**

     * Close modal dengan tombol Escape

     */

    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            const modals = document.querySelectorAll('.modal.modal-open');

            modals.forEach(modal => {

                modal.style.display = 'none';

                modal.classList.remove('modal-open');

                document.body.style.overflow = 'auto';

            });

        }

    });



    /**

     * Show validation errors dalam modal

     */

    function showFormErrors(formId, errors) {

        const form = document.getElementById(formId);

        if (!form) return;



        // Clear previous errors

        form.querySelectorAll('.form-error').forEach(el => el.remove());

        form.querySelectorAll('.form-group.has-error').forEach(el => el.classList.remove('has-error'));



        // Show new errors

        for (const [fieldName, messages] of Object.entries(errors)) {

            const field = form.querySelector(`[name="${fieldName}"]`);

            if (field) {

                const formGroup = field.closest('.form-group');

                if (formGroup) {

                    formGroup.classList.add('has-error');

                    const errorDiv = document.createElement('div');

                    errorDiv.className = 'form-error';

                    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${messages[0]}`;

                    formGroup.appendChild(errorDiv);

                }

            }

        }

    }



    /**

     * Auto-open modal jika ada validation error dari Laravel

     */

    document.addEventListener('DOMContentLoaded', function() {

        const urlParams = new URLSearchParams(window.location.search);

        const modalToOpen = urlParams.get('modal');

       

        if (modalToOpen) {

            openModal(modalToOpen);

        }

    });

</script>