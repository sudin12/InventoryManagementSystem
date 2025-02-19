function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    setTimeout(() => notification.remove(), 3000);
}

function createModal(title, content, primaryButton = null, secondaryButton = null) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>${title}</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                ${content}
                ${primaryButton || secondaryButton ? `
                    <div class="form-actions">
                        ${primaryButton ? `
                            <button class="btn-${primaryButton.type || 'primary'}">${primaryButton.text}</button>
                        ` : ''}
                        ${secondaryButton ? `
                            <button class="btn-${secondaryButton.type || 'secondary'}">${secondaryButton.text}</button>
                        ` : ''}
                    </div>
                ` : ''}
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    const closeBtn = modal.querySelector('.close');
    closeBtn.onclick = () => modal.remove();

    if (primaryButton) {
        modal.querySelector(`.btn-${primaryButton.type || 'primary'}`).onclick = () => {
            primaryButton.onClick(modal);
        };
    }

    if (secondaryButton) {
        modal.querySelector(`.btn-${secondaryButton.type || 'secondary'}`).onclick = () => {
            secondaryButton.onClick(modal);
        };
    }

    window.onclick = (event) => {
        if (event.target === modal) {
            modal.remove();
        }
    };

    return modal;
}

// Apply shared styles
const sharedStyles = document.createElement('style');
sharedStyles.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 4px;
        color: white;
        z-index: 1000;
        animation: slideIn 0.5s ease-out;
    }

    .notification.success { background-color: #4CAF50; }
    .notification.error { background-color: #F44336; }
    .notification.info { background-color: #2196F3; }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* ... rest of your existing modal styles ... */
`;

document.head.appendChild(sharedStyles);
