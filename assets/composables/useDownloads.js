import { ref } from "vue";
//Libraries
import html2pdf from "html2pdf.js";
import Papa from "papaparse";
import ExcelJS from "exceljs";

const useDownloads = (props) => {
  const tableRef = ref(null);
  const tableCopy = ref(null);
  const archiveName = props?.archiveName;
  const hasEdit = props?.hasEdit;

  const tableToJSON = (table) => {
    const data = [];
    const headers = [];
    for (const header of table.querySelectorAll("th")) {
      headers.push(header.textContent);
    }
    for (const row of table.querySelectorAll("tbody tr")) {
      const rowData = [];
      for (const cell of row.querySelectorAll("td")) {
        rowData.push(cell.textContent);
      }
      data.push(rowData);
    }
    return [headers, ...data];
  };

  const trimTable = () => {
    const table = tableRef.value;
    const tableCopied = table.cloneNode(true);
    const rows = tableCopied.querySelectorAll("tr");
    if (archiveName !== "ventas") {
      rows.forEach((row) => {
        const lastCell = row.lastElementChild;
        if (lastCell) {
          row.removeChild(lastCell);
        }
      });
    }
    tableCopy.value = tableCopied;
  };

  const generatePdf = () => {
    const pdfOptions = {
      margin: [15, 7, 15, 7],
      filename: `${archiveName}.pdf`,
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: "mm", format: "a4", orientation: "landscape" },
    };

    if (hasEdit) {
      trimTable();
    }
    if (tableCopy.value) {
      html2pdf(tableCopy.value, pdfOptions);
    } else {
      html2pdf(tableRef.value, pdfOptions);
    }
  };

  const exportToCsv = () => {
    let table = null;
    if (hasEdit) {
      trimTable();
      table = tableCopy.value;
    } else {
      table = tableRef.value;
    }
    const csv = Papa.unparse(tableToJSON(table));
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = `${archiveName}.csv`;
    link.style.display = "none";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const exportToExcel = () => {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Sheet1");
    let table = null;
    if (hasEdit) {
      trimTable();
      table = tableCopy.value;
    } else {
      table = tableRef.value;
    }
    const rows = table.querySelectorAll("tr");

    // Iterar sobre las filas y agregar celdas al archivo Excel
    rows.forEach((row) => {
      const rowData = [];
      row.querySelectorAll("td").forEach((cell) => {
        rowData.push(cell.textContent);
      });
      worksheet.addRow(rowData);
    });

    // Crear un Blob a partir del libro de Excel
    workbook.xlsx.writeBuffer().then((buffer) => {
      const blob = new Blob([buffer], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
      });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = `${archiveName}.xlsx`;
      link.style.display = "none";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
  };

  return {
    tableRef,
    archiveName,
    generatePdf,
    exportToCsv,
    exportToExcel,
  };
};

export default useDownloads;
