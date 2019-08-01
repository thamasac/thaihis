function captureUserMedia(mediaConstraints, successCallback, errorCallback) {
    navigator.mediaDevices.getUserMedia(mediaConstraints).then(successCallback).catch(errorCallback);
}

function onMediaError(e) {
    console.error('media error', e);
}

function xhr(url, data, callback) {
    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
	if (request.readyState == 4 && request.status == 200) {
	    callback(location.href + request.responseText);
	}
    };
    request.open('POST', url);
    request.send(data);
}