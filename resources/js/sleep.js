// 画面スリープ防止機能
let wakeLock = null;
const sleepPreventBtn = document.getElementById("sleepPreventBtn");
let isActive = false;

async function toggleWakeLock() {
    if (!("wakeLock" in navigator)) {
        alert("お使いのブラウザは画面スリープ防止機能に対応していません");
        return;
    }

    try {
        if (wakeLock !== null) {
            // 解除
            await wakeLock.release();
            wakeLock = null;
            isActive = false;
            sleepPreventBtn.style.backgroundColor = "#f5f5f5";
            sleepPreventBtn.style.color = "#ee7800";
        } else {
            // 有効化
            wakeLock = await navigator.wakeLock.request("screen");
            isActive = true;
            sleepPreventBtn.style.backgroundColor = "#fff9c4";
            sleepPreventBtn.style.color = "#ee7800";

            wakeLock.addEventListener("release", () => {
                console.log("Wake Lock was released");
            });
        }
    } catch (err) {
        console.error(`${err.name}, ${err.message}`);
    }
}

sleepPreventBtn.addEventListener("click", toggleWakeLock);

// ページを離れる時に解除
document.addEventListener("visibilitychange", async () => {
    if (wakeLock !== null && document.visibilityState === "visible") {
        wakeLock = await navigator.wakeLock.request("screen");
    }
});
