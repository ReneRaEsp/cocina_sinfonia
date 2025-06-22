import { ref } from "vue";

const usePdf = () => {
  const pdfData = ref(0);

  const scrapePdf = async (ruta) => {
    const URL = `/scrapepdf?ruta=${ruta}`;
    const OPTIONS = {
      method: "GET",
    };
    try {
      let res = await fetch(URL, OPTIONS);
      res = await res.json();
      pdfData.value = res;
      console.log(res);
    } catch (e) {
      console.log(e);
    }
  };

  return {
    //Variables
    pdfData,
    //Funcions
    scrapePdf,
  };
};

export default usePdf;
