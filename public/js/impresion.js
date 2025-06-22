

    var demostrarCapacidades = async (macImpresora, licencia, ticketContent, total) => {
        const URLPlugin = "http://0.0.0.0:8000"; // Si el plugin no está en local, coloca la IP. Por ejemplo 192.168.1.76:8000        
        const conector = new ConectorEscposAndroid(licencia, URLPlugin);
        conector
            .Iniciar()
            .DeshabilitarElModoDeCaracteresChinos()
            .EstablecerAlineacion(ConectorEscposAndroid.ALINEACION_CENTRO)
            .Feed(1)
            .EscribirTexto("Bar Colon\n")
            .EscribirTexto("C. Colon, 5, 08110\n")
            .EscribirTexto("Montcada i Reixac\n")
            .EscribirTexto("Telefono: 930085652\n")
            .EscribirTexto("CIF: 52.174.946k\n")
            .EscribirTexto("Fecha: " + (new Intl.DateTimeFormat("es-ES").format(new Date()))+"\n")
            .EscribirTexto("PROFORMA\n")
            .Feed(1)
            .EstablecerAlineacion(ConectorEscposAndroid.ALINEACION_IZQUIERDA)
            .EscribirTexto("____________________\n")
            .EscribirTexto(ticketContent+"\n")
            .EscribirTexto("____________________\n")
            .EscribirTexto("TOTAL: "+total.toFixed(2)+"\n")
            .EscribirTexto("I.V.A 10% Incluido\n")
            .EscribirTexto("____________________\n")
            .EstablecerAlineacion(ConectorEscposAndroid.ALINEACION_CENTRO)
            .EscribirTexto("Gracias por su visita\n")
            .EstablecerTamañoFuente(1, 1)
            .Corte(1)
            .Pulso(48, 60, 120)

        try {
            const respuesta = await conector.imprimirEn(macImpresora);
            if (respuesta === true) {
                Swal.fire({
                    icon: 'success',
                    title: 'Impreso correctamente',
                    showConfirmButton: false,
                    timer: 1500  // La alerta se cerrará automáticamente después de 1.5 segundos
                  });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: "Error: " + respuesta,
                    showConfirmButton: false,
                    // timer: 1500  // La alerta se cerrará automáticamente después de 1.5 segundos
                  });
            }
        } catch (error) {
            // alert("Error imprimiendo: " + e.message);
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: "Error: " + error,
                showConfirmButton: false,
                // timer: 1500  // La alerta se cerrará automáticamente después de 1.5 segundos
              });
            
        }
    }

    var comprobarCapacidades = async (macImpresora, licencia, ticketContent, total) => {
        const URLPlugin = "http://0.0.0.0:8000";
        const conector = new ConectorEscposAndroid(licencia, URLPlugin);
        
        try {
            const response = await fetch(`${URLPlugin}/imprimir`, {
                method: "POST",
                // body: JSON.stringify(payload),
            });


            if (response.ok) {
                return true;
            } else {
                return true;
            }

        
        } catch (error) {
            console.error("Error en comprobarCapacidades:", error);
            return true;
        }

    }
