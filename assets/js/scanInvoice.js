// scanInvoice.js

const totalum = require('totalum-api-sdk');
const axios = require('axios');


const options = {
    apiKey: {
        'api-key': 'sk-eyJrZXkiOiIzY2U1OTJiZDY0MTEyNTYzMzNmNDFjMjQiLCJuYW1lIjoiRGVmYXVsdCBBUEkgS2V5IGF1dG9nZW5lcmF0ZWQgNDlhbiIsIm9yZ2FuaXphdGlvbklkIjoic2ltb2Z0LWJvYWRpbGxhIn0_'
    }
};
const totalumSdk = new totalum.TotalumApiSdk(options);

async function scanInvoice() {

    // alert('holaaa')
    const fileInput = document.getElementById('fileInput');
    const fileBlob = fileInput.files[0];

    console.log(fileBlob)

    let formData;
    if (typeof process === 'object' && process?.version) {
        FormData = require('form-data');
        formData = new FormData();
    } else {
        formData = new FormData();
    }

    console.log(formData)

    const fileName = fileBlob.name;
    const file = fileBlob;
    formData.append('file', file, fileName);

    console.log(formData)

    const result = await totalumSdk.files.uploadFile(formData);
    const fileNameId = result.data.data;

    const options = {
        getInvoiceItems: false
    };

    const scanResult = await totalumSdk.files.scanInvoice(fileNameId, options);
    const scanData = scanResult.data.data;
    console.log(scanResult);
    console.log(scanData);
}



module.exports = { scanInvoice }

// No necesitas llamar a scanInvoice() aquí si quieres que se ejecute cuando se carga la página.
