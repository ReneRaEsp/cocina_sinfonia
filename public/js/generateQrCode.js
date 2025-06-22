// generateQrCode.js
function generateAndDownloadQRCode(qrData, fileName) {
    const qr = new QRious({
        element: null,
        value: qrData,
        size: 256,
    });

    const qrDataURL = qr.toDataURL();
    const link = document.createElement('a');
    link.href = qrDataURL;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
