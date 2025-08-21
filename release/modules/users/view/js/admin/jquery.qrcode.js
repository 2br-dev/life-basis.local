/**
 * Инициализирует таймер обратного отсчета для срока действия авторизационного QR-кода
 */
$(function() {
    let timer;

    $.contentReady(function(event) {
        let div =  this.querySelector('[data-admin-qr-left-seconds]');
        if (div) {
            let minutesDiv = div.querySelector('.admin-qr-left-minutes');
            let secondsDiv = div.querySelector('.admin-qr-left-seconds');
            let seconds = div.dataset.adminQrLeftSeconds;

            clearInterval(timer);
            timer = setInterval(() => {
                seconds--;
                let min = Math.floor(seconds / 60);
                let sec = seconds % 60;
                minutesDiv.innerText = (min < 10) ? '0' + min : min;
                secondsDiv.innerText = (sec < 10) ? '0' + sec : sec;

                if (seconds <= 0 || !div.isConnected) {
                    div.innerText = div.dataset.adminQrExpireText;
                    clearInterval(timer);
                }
            }, 1000);
        }
    });

});