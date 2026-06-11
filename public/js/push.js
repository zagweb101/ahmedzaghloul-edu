(() => {
    const button = document.querySelector('[data-push-enable]');

    if (!button || !('serviceWorker' in navigator) || !('PushManager' in window)) {
        return;
    }

    const publicKey = button.dataset.vapidPublicKey;
    const storeUrl = button.dataset.storeUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!publicKey || !storeUrl || !csrfToken) {
        button.disabled = true;
        return;
    }

    const urlBase64ToUint8Array = (base64String) => {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; i += 1) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    };

    button.addEventListener('click', async () => {
        button.disabled = true;

        try {
            const permission = await Notification.requestPermission();

            if (permission !== 'granted') {
                button.textContent = 'لم يتم السماح بالإشعارات';
                return;
            }

            const registration = await navigator.serviceWorker.ready;
            const subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey),
            });

            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: JSON.stringify(subscription.toJSON()),
            });

            if (!response.ok) {
                throw new Error('store_failed');
            }

            button.textContent = 'تم تفعيل إشعارات المتصفح';
            button.classList.replace('btn-soft', 'btn-brand');
        } catch (error) {
            button.disabled = false;
            button.textContent = 'تعذر تفعيل الإشعارات';
        }
    });
})();
