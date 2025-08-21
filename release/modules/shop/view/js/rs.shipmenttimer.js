document.addEventListener('DOMContentLoaded', function() {
    let bindTimer = (it) => {
        if (it.dataset.shipmentTimer) return;
        it.dataset.shipmentTimer = true;
        it.textContent = '';
        let deadline = Date.parse(it.dataset.timestamp);

        let hoursEl = document.createElement('span');
        hoursEl.title = lang.t('Часов');
        it.appendChild(hoursEl);

        let hoursElDev = document.createElement('span');
        hoursElDev.textContent = ':';
        it.appendChild(hoursElDev);

        let minuteEl = document.createElement('span');
        minuteEl.title = lang.t('Минут');
        it.appendChild(minuteEl);

        let minuteElDev = document.createElement('span');
        minuteElDev.textContent = ':';
        it.appendChild(minuteElDev);

        let secondEl = document.createElement('span');
        secondEl.title = lang.t('Секунд');
        it.appendChild(secondEl);

        let timerId = null;

        // вычисляем разницу дат и устанавливаем оставшееся времени в качестве содержимого элементов
        function countdownTimer() {
            const diff = deadline - new Date();
            if (diff <= 0) {
                clearInterval(timerId);
            }

            if (diff / 1000 < (60*60*4)) {
                it.style.color = 'red';
            } else if (diff / 1000 < (60*60*12)) {
                it.style.color = 'orange';
            }

            const hours = diff > 0 ? Math.floor(diff / 1000 / 60 / 60) : 0;
            const minutes = diff > 0 ? Math.floor(diff / 1000 / 60) % 60 : 0;
            const seconds = diff > 0 ? Math.floor(diff / 1000) % 60 : 0;

            hoursEl.textContent = hours < 10 ? '0' + hours : hours;
            minuteEl.textContent = minutes < 10 ? '0' + minutes : minutes;
            secondEl.textContent = seconds < 10 ? '0' + seconds : seconds;
        }

        countdownTimer();
        // вызываем функцию countdownTimer каждую секунду
        timerId = setInterval(countdownTimer, 1000);
    };

    document.querySelectorAll('.shipment-timer').forEach(bindTimer);
    $.contentReady(() => {
        document.querySelectorAll('.shipment-timer').forEach(bindTimer);
    });
});