<template>
  <form class="bg-light" @submit.prevent="submitForm">
    <v-text-field
      v-model="state.name"
      :error-messages="v$.name?.$errors.map((e) => e.$message)"
      :counter="10"
      label="Nombre"
      required
      @input="v$.nombre?.$touch"
      @blur="v$.nombre?.$touch"
    ></v-text-field>
    <v-text-field
      v-model="state.pvp"
      :error-messages="v$.pvp?.$errors.map((e) => e.$message)"
      label="Precio"
      required
      type="number"
      @input="v$.pvp?.$touch"
      @blur="v$.pvp?.$touch"
    ></v-text-field>
    <v-btn color="light-blue-darken-4" class="me-4" @click="submitForm">
      Guardar
    </v-btn>
    <!--<v-btn @click="clear"> Clear </v-btn>-->
  </form>
</template>
  
  <script setup>
  import useTienda from "./../../../composables/useTienda";
 
  
  const props = defineProps({
    editData: {
      type: Object,
      required: false,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
    selectProveedores: {
      type: Array,
    },
    isActive: {
      type: Boolean,
    },
  });
  
  const { state, items, v$, submitForm, clear } = useTienda(props);

  </script>
  