import { defineStore } from "pinia";

export const useVentasStore = defineStore("ventasStore", {
  state: () => ({
    showVentasModal: false,
  }),
  actions: {
    setVentasModal(newstatus) {
      this.showVentasModal = newstatus;
    },
  },
});
