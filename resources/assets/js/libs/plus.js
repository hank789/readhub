function plusReady(callback) {
    if (window.plus) {
        setTimeout(function () {
            callback();
        }, 0);
    } else {
        document.addEventListener("plusready", function () {
            callback();
        }, false);
    }
}

export {
    plusReady
};
