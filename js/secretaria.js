var actualizar = false;
campos = new Array();
encabezamiento = new Array();
var anno_ini_curso = 0;
var mes;
var _orden_campo;
var _orden_direccion;
var incidencia_si = 0;
var curso_actual,curso_premat,curso_mat;
var sesion_id;
var tipo_formulario="";
var n_reg="";
var ciclos_gb=new Array();
var ciclos_gm=new Array();  
var ciclos_gm_gs=new Array();
var ciclos_gs=new Array();
var departamentos=new Array();

$(function() {
    if (document.location.hostname!="registro.ulaboral.org")document.getElementById("servidor_pruebas").style.display="inherit";
    else document.getElementById("servidor_pruebas").style.display="none";
    generaSelectCurso_pre_mat();
    generaSelectCurso_mat();
    
    mostrarPantallaEspera();
    prom1=Promise.resolve($.post("php/sesion.php", { tipo_usu: "secretaria" },()=>{},"json"));
    prom2=prom1.then((resp)=> {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            if (resp["tipo_usu"]=="jefatura estudios"){
                document.getElementById("rotulo_tipo_usu").innerHTML="JEFATURA ESTUDIOS - GESTIÓN DEL REGISTRO ONLINE";
                document.getElementById("menu1").classList.add("disabled");
                document.getElementById("borra_premat").style.display="";
                document.getElementById("divider_borra_premat").style.display="";
            }
            anno_ini_curso = resp["anno_ini_curso"];
            anno_ini_curso_docs = resp["anno_ini_curso_docs"];
            mes = resp["mes"];
            _curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            curso_actual=_curso;
            if (mes<=6 && mes>=1) curso_premat=_curso;
            else curso_premat=(anno_ini_curso-1)+"-"+(anno_ini_curso);
            if(mes!=6)curso_mat=anno_ini_curso+"-"+(anno_ini_curso + 1);
            else curso_mat=(anno_ini_curso+1)+"-"+(anno_ini_curso + 2);
            generaSelectCurso(document.getElementById("curso"));
            document.getElementById("curso").value = _curso;
            if (document.getElementById("curso").value != "2020-2021") $("#curso_pre_mat option[value='3esopmar']").hide();
            else $("#curso_pre_mat option[value='3esopmar']").show();
            ocultaCursosDesplegable();
            return($.post("php/secret_prematricula.php", { peticion: "read" },()=>{},"json"));
        }
    });

    prom3=prom2.then((resp)=> {
        document.getElementById("premat_eso").checked = (resp["eso"] == 0 ? false : true);
        document.getElementById("premat_bach").checked = (resp["bach"] == 0 ? false : true);
        return ($.post("php/secret_matricula.php", { peticion: "read" },()=>{},"json"));
    });

    prom4=prom3.then((resp)=>{
        document.getElementById("check_mat_eso").checked = (resp["eso"] == 0 ? false : true);
        document.getElementById("check_mat_bach").checked = (resp["bach"] == 0 ? false : true);
        document.getElementById("check_mat_ciclos").checked = (resp["ciclos"] == 0 ? false : true);
        document.getElementById("check_mat_ciclos-e").checked = (resp["ciclo_e"] == 0 ? false : true);
        document.getElementById("check_mat_fpb").checked = (resp["fpb"] == 0 ? false : true);
        return ($.post("php/secret_num_reg_sinrevisar.php", {curso:curso_actual},()=>{},"json"));
        
    });
    prom5=prom4.then((resp)=>{
        ocultarPantallaEspera();
        if (resp.error=="ok"){
            generaSelectTipo_form(resp.datos);
        }
        else if(resp.error="server"){
            alerta("Error en base de datos. La aplicación no funcionará correctamente.","ERROR DB");
        }
        return ($.post('php/secret_recupera_departamentos.php',{},()=>{},"json"));
    });
    prom6=prom5.then((resp)=>{
        if (resp.error=="ok"){
            for(i=0;i<resp.registro.length;i++){
                departamentos.push(new Array(resp.registro[i].departamento,resp.registro[i].abreviatura,resp.registro[i].email_jd,resp.registro[i].id));
            }
            generaSelectsDepartamentos();
        }
        else {
            alerta("Ha habido algún error con la base de datos o el servidor. Las exenciones de formación en empresa no funcionarán correctamente","ERROR DB/SERVIDOR");
        }
        return ($.post('impresos/exencion_fct/php/ciclos.php',{},()=>{},"json"));  
    });
    prom7=prom6.then((resp)=>{
        const option = document.createElement("option");
        option.value="";
        option.text="Seleccione uno...";
        option.selected=true;
        document.getElementById("mat_ciclos").add(option);
        for (i=0; i<resp.datos.length; i++){
            if (resp.datos[i].grado === "SUPERIOR" || resp.datos[i].grado === "MEDIO") {
                let prefijo = "";
                if (resp.datos[i].grado === "SUPERIOR") {prefijo = "GS";}
                else if (resp.datos[i].grado === "MEDIO") {prefijo = "GM";}
                const option = document.createElement("option");
                option.value = resp.datos[i].denominacion;
                option.text = prefijo + " " + resp.datos[i].denominacion;
                document.getElementById("mat_ciclos").add(option);
            }
        } 

        const option2 = document.createElement("option");
        option2.value="";
        option2.text="Seleccione uno...";
        option2.selected=true;
        document.getElementById("mat_fpb").add(option2);
        for (i=0; i<resp.datos.length; i++){
            if (resp.datos[i].grado === "BÁSICO") {
                const option = document.createElement("option");
                option.value = resp.datos[i].denominacion;
                option.text = resp.datos[i].denominacion;
                document.getElementById("mat_fpb").add(option);
            }
        } 

        for (i=0; i<resp.datos.length; i++){
            if (resp.datos[i].grado == "BÁSICO") {
                ciclos_gb.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
            if (resp.datos[i].grado == "MEDIO") {
                ciclos_gm.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
                ciclos_gm_gs.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
            if (resp.datos[i].grado == "SUPERIOR") {
                ciclos_gs.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
                ciclos_gm_gs.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
        }
        
    });

    habilitaMenu(false, false); 

    $('#registros_docs').contextMenu({
        selector: 'tr',
        callback: function(key, options) {
            if ($("#encabezado_docs tr td:first").html() == "NIE") {
                id = $(this).children("td:first").html();
                nombre = $(this).children("td:nth-child(2)").html();
                if($("#encabezado_docs tr td:nth-child(4)").html() == "Centro"){
                    n_reg=$(this).children("td:nth-child(2)").attr("title");
                }
                else if($("#encabezado_docs tr td:nth-child(4)").html() == "Registro"){
                    n_reg=$(this).children("td:nth-child(4)").html();
                }
                else {
                    n_reg=$(this).children("td:nth-child(3)").html();
                }
            } else if ($("#encabezado_docs tr td:nth-child(2)").html() == "NIE"){
                id = $(this).children("td:nth-child(2)").html();
                nombre = $(this).children("td:nth-child(3)").html();
                n_reg=$(this).children("td:nth-child(4)").html();
            }
            if (key == "edit") {
                panelModUsu(id);
            } else if (key=="delete") {
                eliminaUsuario(id);
            }
            else if(key=="cert"){
                verCertificado(id);
            }
            else if(key=="exp"){
                verExpediente(id,nombre);
            }
            else if(key=="docs"){
                verDocsMatricula(id,0);
            }
            
        },
        items: {
            "edit": { name: "Ver/Modificar datos de usuario", icon: "edit" },
            "docs":{name:"Ver Docs. Matrícula", icon:"copy"},
            "cert":{ name:"Ver certificado", icon:"copy"},
            "exp":{name:"Ver Expediente", icon:"copy"},
            "delete": {name: "Eliminar usuario", icon: "delete" }
        }
    });
});

function generaSelectCurso(obj){
    if (mes<6) a_final=anno_ini_curso;
    else a_final=anno_ini_curso+1;

    const miSelect = obj;
    for (var i=2020;i<=a_final;i++){
        const elemento = document.createElement("option");
        elemento.value = i+"-"+(parseInt(i)+1);
        elemento.textContent = elemento.value;
        miSelect.appendChild(elemento);
    }
}

function obtenRegSinProcesar(){
    mostrarPantallaEspera();
    $.post("php/secret_num_reg_sinrevisar.php", {curso:document.getElementById("curso").value},(resp)=>{
        if (resp.error=="ok"){
            generaSelectTipo_form(resp.datos);
        }
        ocultarPantallaEspera();
    },"json")
}

function generaSelectTipo_form(matriz){
    // Obtener el elemento select
    const miSelect = document.getElementById("tipo_form");
    miSelect.innerHTML="";
    // Crear la opción "Seleccione uno..." con el valor vacío y seleccionada por defecto
    const opcion0 = document.createElement("option");
    opcion0.value = "";
    opcion0.selected = false;
    opcion0.textContent = "Seleccione uno...";
    miSelect.appendChild(opcion0);

    // Crear las opciones restantes
    const opciones = [
        { value: "convalidaciones", text: "Convalidaciones ("+matriz.convalidaciones+")" },
        { value: "exencion_fct", text: "Exencion PFE ("+matriz.exencion_fct+")" },
        { value: "matricula_ciclos", text: "Matrícula CICLOS" },
        { value: "matricula", text: "Matrícula ESO y BACH" },
        { value: "matricula_fpb", text: "Matrícula FPB" },
        { value: "prematricula", text: "Prematrícula" },
        { value: "revision_calificacion", text: "Revisión de calificación ("+matriz.revision_calificacion+")" },
        { value: "revision_examen", text: "Revisión de examen ("+matriz.revision_examen+")" }
    ];

    // Recorrer el array de opciones y crear las opciones
    for (const opcion of opciones) {
        const elemento = document.createElement("option");
        elemento.value = opcion.value;
        elemento.textContent = opcion.text;
        miSelect.appendChild(elemento);
    }

    document.getElementById("tipo_form").value=tipo_formulario;
}


function generaSelectCurso_pre_mat(){
    // Obtener el elemento select
    const miSelect = document.getElementById("curso_pre_mat");

    // Crear la opción "Seleccione uno..." con el valor vacío y seleccionada por defecto
    const opcion0 = document.createElement("option");
    opcion0.value = "";
    opcion0.selected = true;
    opcion0.textContent = "Seleccione uno...";
    miSelect.appendChild(opcion0);

    // Crear las opciones restantes
    const opciones = [
    { value: "2eso", text: "2º ESO" },
    { value: "3eso", text: "3º ESO" },
    { value: "3esodiv", text: "3º ESO DIV" },
    { value: "4eso", text: "4º ESO" },
    { value: "4esodiv", text: "4º ESO DIV" },
    { value: "1bach_c", text: "1º BACH CIENCIAS Y TEC."},//Con itemporp, por si fuera necesario { value: "1bach", text: "1º BACHILLERATO", itemprop: "2021-2022" }
    { value: "1bach_h", text: "1º BACH HH.CC.SS."},
    //{ value: "1bach_g", text: "1º BACH GENERAL"},
    { value: "2bach_c", text: "2º BACH CIENCIAS Y TEC." },
    { value: "2bach_h", text: "2º BACH HH.CC.SS." }
    ];

    // Recorrer el array de opciones y crear las opciones
    for (const opcion of opciones) {
    const elemento = document.createElement("option");
    elemento.value = opcion.value;
    elemento.textContent = opcion.text;
    
    // Agregar el atributo "itemprop" si existe en el objeto
    if (opcion.itemprop) {
        elemento.setAttribute("itemprop", opcion.itemprop);
    }
    
    miSelect.appendChild(elemento);
    }
}

function generaSelectCurso_mat(){
    _sel_curso=document.getElementById("curso").value;
    if (_sel_curso=="2020-2021" || _sel_curso=="2021-2022"){
        var options = [
            {value: "", text: "Seleccione uno..."},
            {value: "1eso", text: "1º ESO"},
            {value: "2eso", text: "2º ESO"},
            {value: "2esopmar", text: "2º ESO PMAR"},
            {value: "3eso", text: "3º ESO"},
            {value: "3esopmar", text: "3º ESO PMAR"},
            {value: "4eso", text: "4º ESO"},
            {value: "1bach_c", text: "1º Bach. Ciencias"},
            {value: "1bach_hcs", text: "1º Bach. HH.CC.SS."},
            {value: "2bach_c", text: "2º Bach. Ciencias"},
            {value: "2bach_hcs", text: "2º Bach. HH.CC.SS."}
        ];
    }
    else if (_sel_curso=="2022-2023"){
        var options = [
            {value: "", text: "Seleccione uno..."},
            {value: "1eso", text: "1º ESO"},
            {value: "2eso", text: "2º ESO"},
            {value: "2esopmar", text: "2º ESO PMAR"},
            {value: "3eso", text: "3º ESO"},
            {value: "3esodiv", text: "3º ESO DIVERSIFICACIÓN"},
            {value: "4eso", text: "4º ESO"},
            {value: "1bach_c", text: "1º Bach. Ciencias"},
            {value: "1bach_hcs", text: "1º Bach. HH.CC.SS."},
            {value: "2bach_c", text: "2º Bach. Ciencias"},
            {value: "2bach_hcs", text: "2º Bach. HH.CC.SS."}
        ];
    }
    else{
        var options = [
            {value: "", text: "Seleccione uno..."},
            {value: "1eso", text: "1º ESO"},
            {value: "2eso", text: "2º ESO"},
            {value: "3eso", text: "3º ESO"},
            {value: "3esodiv", text: "3º ESO DIVERSIFICACIÓN"},
            {value: "4eso", text: "4º ESO"},
            {value: "4esodiv", text: "4º ESO DIVERSIFICACIÓN"},
            {value: "1bach_c", text: "1º Bach. Ciencias y Tecnología"},
            {value: "1bach_hcs", text: "1º Bach. HH.CC.SS."},
            {value: "2bach_c", text: "2º Bach. Ciencias y Tecnología"},
            {value: "2bach_hcs", text: "2º Bach. HH.CC.SS."}
        ];
    }
    
      
    var select = document.getElementById("curso_mat");
    
    for (var i = 0; i < options.length; i++) {
        var option = document.createElement("option");
        option.value = options[i].value;
        option.text = options[i].text;
        if (options[i].hasOwnProperty("itemprop")) {
            option.setAttribute("itemprop", options[i].itemprop);
        }
        if (i === 0) {
            option.selected = true;
        }
        select.appendChild(option);
    } 
}


function generaSelectCursoTurnoGMGS(c){
    if (c != "") {
        arr=ciclos_gm_gs;
        for(i=0; i<arr.length; i++){
            if (arr[i][0]==c){
                cicloArr=arr[i];
                break;
            } 
        }
        document.getElementById("mat_ciclos_curso").innerHTML = "";
        cu = "<option value=''>...</option>";
        cu += "<option value='1º'>1º</option>";
        if (cicloArr[1]>=2 )cu += "<option value='2º'>2º</option>";
        if (cicloArr[1]==3) cu += "<option value='3º'>3º</option>";
        cu += "<option value='Modular'>Modular</option>";
        if(cicloArr[5]==1) cu += "<option value='learning'>E-Learning</option>";
        document.getElementById("mat_ciclos_curso").innerHTML = cu;

        document.getElementById("mat_ciclos_turno").innerHTML = "";
        tu = "<option value=''>...</option>";
        if (cicloArr[2] == 1) tu += "<option value='Diurno'>Diurno</option>";
        if (cicloArr[3] == 1) tu += "<option value='Vespertino'>Vespertino</option>";
        if (cicloArr[4] == 1) tu += "<option value='Nocturno'>Nocturno</option>";
        document.getElementById("mat_ciclos_turno").innerHTML = tu;
    }
}


function generaSelectsDepartamentos(){
    document.getElementById("departamento").innerHTML="";
    if (document.getElementById("config_dpto"))document.getElementById("config_dpto").innerHTML="";
    if (document.getElementById("dpto_select"))document.getElementById("dpto_select").innerHTML="";
    opt=document.createElement("option");
    opt.value="Todos";
    opt.textContent="Todos";
    opt.dataset.email="todos";
    document.getElementById("departamento").appendChild(opt);
    opt=document.createElement("option");
    opt.value="";
    opt.textContent="Seleccione departamento ...";
    if (document.getElementById("config_dpto"))document.getElementById("config_dpto").appendChild(opt);
    for(i=0;i<departamentos.length;i++){
        opt=document.createElement("option");
        opt.value=departamentos[i][0];
        opt.textContent=departamentos[i][0] +" ("+departamentos[i][1]+")";
        if (document.getElementById("config_dpto"))document.getElementById("config_dpto").appendChild(opt);
        opt=document.createElement("option");
        opt.value=departamentos[i][0];
        opt.textContent=departamentos[i][0] +" ("+departamentos[i][1]+")";
        opt.dataset.email=departamentos[i][2];
        document.getElementById("departamento").appendChild(opt);
        opt=document.createElement("option");
        opt.value=departamentos[i][0];
        opt.textContent=departamentos[i][0] +" ("+departamentos[i][1]+")";
        opt.dataset.id=departamentos[i][3];
        opt.dataset.abreviatura=departamentos[i][1];
        if (document.getElementById("dpto_select"))document.getElementById("dpto_select").appendChild(opt);
    }
}



function listaRegistros(orden_campo, orden_direccion) {
    document.getElementById("div_incidencias").style.display="inherit";
    document.getElementById("div_convalidaciones").style.display="none";
    document.getElementById("div_exencion_fct").style.display="none";
    ocultaCursosDesplegable();
    tipo_formulario = document.getElementById('tipo_form').value;
    if (tipo_formulario == "prematricula") {
        habilitaMenu(false, false);
        document.getElementById("div_curso_premat").style.display = "inherit";
        document.getElementById("div_curso_mat").style.display = "none";
        document.getElementById("div_curso_mat_ciclos").style.display = "none";
        document.getElementById("div_curso_mat_fpb").style.display = "none";
        if (document.getElementById("curso_pre_mat").value != "") $("#CSV_premat").removeClass("disabled");
        else $("#CSV_premat").addClass("disabled");
        //$("#menu_csv_mat").addClass("disabled");
        //$("#menu_listado_mat_pdf").addClass("disabled");
        $("#CSV_transporte").addClass("disabled");
        $("#CSV_seguro").addClass("disabled");
    } else if (tipo_formulario == "matricula") {
        habilitaMenu(true, false);
        document.getElementById("div_curso_mat").style.display = "inherit";
        document.getElementById("div_curso_premat").style.display = "none";
        document.getElementById("div_curso_mat_ciclos").style.display = "none";
        document.getElementById("div_curso_mat_fpb").style.display = "none";
        $("#CSV_premat").addClass("disabled");
        if (document.getElementById("curso_mat").value != "") {
            //$("#menu_csv_mat").removeClass("disabled");
            //$("#menu_listado_mat_pdf").removeClass("disabled");
            $("#CSV_transporte").removeClass("disabled");
            $("#CSV_seguro").addClass("disabled");
        } else {
            //$("#menu_csv_mat").addClass("disabled");
            //$("#menu_listado_mat_pdf").addClass("disabled");
            $("#CSV_transporte").addClass("disabled");
            $("#CSV_seguro").addClass("disabled");
        }
    } else if (tipo_formulario == "matricula_ciclos") {
        habilitaMenu(true, false);
        document.getElementById("div_curso_mat").style.display = "none";
        document.getElementById("div_curso_premat").style.display = "none";
        document.getElementById("div_curso_mat_ciclos").style.display = "inherit";
        document.getElementById("div_curso_mat_fpb").style.display = "none";
        $("#CSV_premat").addClass("disabled");
        //$("#menu_csv_mat").addClass("disabled");
        //$("#menu_listado_mat_pdf").addClass("disabled");
        if (document.getElementById("mat_ciclos").value != "" &&
            document.getElementById("mat_ciclos_curso").value != "" &&
            document.getElementById("mat_ciclos_turno").value != "") {
            $("#CSV_transporte").addClass("disabled");
            $("#CSV_seguro").removeClass("disabled");
        } else {
            $("#CSV_transporte").addClass("disabled");
            $("#CSV_seguro").addClass("disabled");
        }
    } else if (tipo_formulario == "matricula_fpb") {
        habilitaMenu(true, false);
        document.getElementById("div_curso_mat").style.display = "none";
        document.getElementById("div_curso_premat").style.display = "none";
        document.getElementById("div_curso_mat_ciclos").style.display = "none";
        document.getElementById("div_curso_mat_fpb").style.display = "inherit";
        $("#CSV_premat").addClass("disabled");
        //$("#menu_csv_mat").addClass("disabled");
        //$("#menu_listado_mat_pdf").addClass("disabled");
        if (document.getElementById("mat_fpb").value != "" &&
            document.getElementById("mat_fpb_curso").value != "") {
            $("#CSV_transporte").removeClass("disabled");
            $("#CSV_seguro").addClass("disabled");
        } else {
            $("#CSV_transporte").addClass("disabled");
            $("#CSV_seguro").addClass("disabled");
        }
    }
    else if(tipo_formulario=="convalidaciones" || tipo_formulario=="exencion_fct"){
        habilitaMenu(false, false);
        document.getElementById("div_incidencias").style.display="none";
        document.getElementById("div_convalidaciones").style.display="inherit";
        if (tipo_formulario=="exencion_fct") document.getElementById("div_exencion_fct").style.display="";
        document.getElementById("div_curso_premat").style.display = "none";
        document.getElementById("div_curso_mat").style.display = "none";
        document.getElementById("div_curso_mat_ciclos").style.display = "none";
        document.getElementById("div_curso_mat_fpb").style.display = "none";
        $("#CSV_premat").addClass("disabled");
        //$("#menu_csv_mat").addClass("disabled");
        //$("#menu_listado_mat_pdf").addClass("disabled");
        $("#CSV_transporte").addClass("disabled");
        $("#CSV_seguro").addClass("disabled");
    }
    else {
        habilitaMenu(true, true);
        document.getElementById("div_curso_premat").style.display = "none";
        document.getElementById("div_curso_mat").style.display = "none";
        document.getElementById("div_curso_mat_ciclos").style.display = "none";
        document.getElementById("div_curso_mat_fpb").style.display = "none";
        $("#CSV_premat").addClass("disabled");
        //$("#menu_csv_mat").addClass("disabled");
        //$("#menu_listado_mat_pdf").addClass("disabled");
        $("#CSV_transporte").addClass("disabled");
        $("#CSV_seguro").addClass("disabled");
    }

    direccion = new Array();
    direccion["🡅"] = "ASC";
    direccion["🡇"] = "DESC";
    curso_num="";
    $("#div_nuevos_otra_comunidad").hide();
    if (tipo_formulario == "revision_examen") {
        tabla = tipo_formulario;
        campos = ["id_nie", "nombre", "del_alumno", "registro"];
        estilo = ["width:70px", "width:210px", "width:200px", "width:270px"];
        encabezamiento = ["NIE", "Solicitante", "Alumno", "Nº Registro"];
    } else if (tipo_formulario == "revision_calificacion") {
        tabla = tipo_formulario;
        campos = ["id_nie", "nombre", "registro"];
        estilo = ["width:70px", "width:220px", "width:270px"];
        encabezamiento = ["NIE", "Solicitante", "Nº Registro"];
    } else if(tipo_formulario=="convalidaciones"){
        tabla = tipo_formulario;
        campos = ["id_nie", "nombre", "fecha_registro","resuelve_cen","resuelto_cen","resuelve_con","resuelto_con","resuelve_min","resuelto_min"];
        estilo = ["width:70px", "width:220px", "width:85px;text-align:center;", "width:70px;text-align:center;", "width:70px;text-align:center;", "width:70px;text-align:center;", "width:70px;text-align:center;", "width:70px;text-align:center;", "width:70px;text-align:center;", "width:70px;text-align:center;" ];
        encabezamiento = ["NIE", "Alumno", "Fecha Reg.","Centro","Proc.Centro","Consej.","Proc.Cons.","Minist.","Proc.Minist.","Visto"];
    } else if(tipo_formulario=="exencion_fct"){
        tabla = tipo_formulario;
        campos = ["id_nie", "nombre", "fecha_registro","registro","resolucion"];
        estilo = ["width:70px", "width:180px", "width:85px;text-align:center;", "width:230px;", "width:150px;text-align:center;" ];
        encabezamiento = ["NIE", "Alumno", "Fecha Reg.","Registro","Resolución"];
    } else if (tipo_formulario == "prematricula") {
        if (document.getElementById("curso_pre_mat").value == "2eso"){tabla = "premat_eso"; grupo="2º ESO";}
        else if (document.getElementById("curso_pre_mat").value == "3eso") {tabla = "premat_eso"; grupo="3º ESO";}
        else if (document.getElementById("curso_pre_mat").value == "3esodiv") {tabla = "premat_eso"; grupo="3º ESO DIV";}
        else if (document.getElementById("curso_pre_mat").value == "4eso") {tabla = "premat_eso"; grupo="4º ESO";}
        else if (document.getElementById("curso_pre_mat").value == "4esodiv") {tabla = "premat_eso"; grupo="4º ESO DIV";}
        else if (document.getElementById("curso_pre_mat").value == "1bach_c") {tabla = "premat_bach"; grupo="1º Bachillerato"; modalidad="Ciencias y Tecnología";}
        else if (document.getElementById("curso_pre_mat").value == "1bach_h") {tabla = "premat_bach"; grupo="1º Bachillerato"; modalidad="Humanidades y Ciencias Sociales";}
        else if (document.getElementById("curso_pre_mat").value == "1bach_g") {tabla = "premat_bach"; grupo="1º Bachillerato"; modalidad="General";}
        else if (document.getElementById("curso_pre_mat").value == "2bach_c") {tabla = "premat_bach"; grupo="2º Bach. Ciencias y Tecnología";modalidad="";}
        else if (document.getElementById("curso_pre_mat").value == "2bach_h") {tabla = "premat_bach"; grupo="2º Bach. HH.CC.SS.";modalidad="";}
        else return;
        campos = ["id_nie", "nombre", "registro"];
        estilo = ["width:70px", "width:260px", "width:260px"];
        encabezamiento = ["NIE", "Alumno", "Nº Registro"];
    } else if (tipo_formulario == "matricula") {
        if (document.getElementById("curso").value=="2021-2022" || document.getElementById("curso").value=="2020-2021"){
            if (document.getElementById("curso_mat").value == "1eso") tabla = "mat_1eso";
            else if (document.getElementById("curso_mat").value == "2eso") tabla = "mat_2eso";
            else if (document.getElementById("curso_mat").value == "3eso") tabla = "mat_3eso";
            else if (document.getElementById("curso_mat").value == "4eso") tabla = "mat_4eso";
            else if (document.getElementById("curso_mat").value == "2esopmar") tabla = "mat_2esopmar";
            else if (document.getElementById("curso_mat").value == "3esopmar") tabla = "mat_3esopmar";
            else if (document.getElementById("curso_mat").value == "1bach_c") tabla = "mat_1bach_c";
            else if (document.getElementById("curso_mat").value == "1bach_hcs") tabla = "mat_1bach_hcs";
            else if (document.getElementById("curso_mat").value == "2bach_c") tabla = "mat_2bach_c";
            else if (document.getElementById("curso_mat").value == "2bach_hcs") tabla = "mat_2bach_hcs";
            else return;
        }
        else{
            $("#div_nuevos_otra_comunidad").show();
            if (document.getElementById("curso_mat").value == "1eso") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "2eso") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "3eso") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "4eso") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "2esopmar") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "3esodiv") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "4esodiv") tabla = "mat_eso";
            else if (document.getElementById("curso_mat").value == "1bach_c") tabla = "mat_bach";
            else if (document.getElementById("curso_mat").value == "1bach_hcs") tabla = "mat_bach";
            else if (document.getElementById("curso_mat").value == "2bach_c") tabla = "mat_bach";
            else if (document.getElementById("curso_mat").value == "2bach_hcs") tabla = "mat_bach";
            else return;
            _c_mat=document.getElementById("curso_mat");
            curso_num=_c_mat.options[_c_mat.selectedIndex].text;
            /*if (document.getElementById("curso").value!="2021-2022" && document.getElementById("curso").value!="2020-2021" && document.getElementById("curso").value!="2022-2023"){
                if (curso_num=="1º Bach. Ciencias") curso_num="1º Bach. Ciencias y Tecnología";
                else if(curso_num=="2º Bach. Ciencias") curso_num="2º Bach. Ciencias y Tecnología";
            }*/
        }
        
        if (tabla.indexOf("eso") > -1) {
            campos = ["id_nie", "nombre", "registro", "consolida_premat", "transporte"];
            estilo = ["width:70px", "width:260px", "width:260px", "width:40px", "width:30px"];
            encabezamiento = ["NIE", "Alumno", "Nº Registro", "Cons.", "Tr."];
            if (tabla.indexOf("3eso") > -1 || tabla.indexOf("4eso") > -1) {
                estilo.push("width:40px");
                encabezamiento.push("Docs");
            }
        } else if (tabla.indexOf("bach") > -1) {
            campos = ["id_nie", "nombre", "registro", "consolida_premat"];
            estilo = ["width:70px", "width:260px", "width:260px", "width:40px", "width:40px"];
            encabezamiento = ["NIE", "Alumno", "Nº Registro", "Cons.", "Docs"];
        }

    } else if (tipo_formulario == "matricula_ciclos") {
        $("#div_nuevos_otra_comunidad").show();
        tabla = "mat_ciclos";
        campos = ["id_nie", "nombre", "registro"];
        estilo = ["width:70px", "width:260px", "width:260px", "width:40px", "width:40px"];
        encabezamiento = ["NIE", "Alumno", "Nº Registro", "Docs", ">28"];
    } else if (tipo_formulario == "matricula_fpb") {
        $("#div_nuevos_otra_comunidad").show();
        tabla = "mat_fpb";
        campos = ["id_nie", "nombre", "registro"];
        estilo = ["width:70px", "width:260px", "width:260px", "width:40px"];
        encabezamiento = ["NIE", "Alumno", "Nº Registro", "Docs"];
    }

    if (typeof(orden_campo) != "string") {
        if (tipo_formulario=="convalidaciones")orden_campo="fecha_registro";
        else orden_campo = "apellidos,nombre";
        _orden_campo = orden_campo;
    }
    if (typeof(orden_direccion) != "string") {
        if (tipo_formulario=="convalidaciones") orden_direccion = "🡇";
        else orden_direccion = "🡅";
        _orden_direccion = orden_direccion;
    }
    if (document.getElementById('tipo_form').value === "") {
        //alerta("Debes seleccionar antes un tipo de formulario.", "SIN SELECCIÓN");
        return;
    }

    //Construcción del encabezamiento de la tabla
    if(tipo_formulario=="convalidaciones" || tipo_formulario=="exencion_fct"){
        if (orden_campo == "apellidos") encabezamiento[1] += " " + orden_direccion;
        else encabezamiento[campos.indexOf(orden_campo)] += " " + orden_direccion;
        encab = "<tr>";
        for (i = 0; i < encabezamiento.length; i++) {
            if(encabezamiento[i].substr(0,3)=="NIE" || encabezamiento[i].substr(0,6)=="Alumno" || encabezamiento[i].substr(0,10)=="Fecha Reg."){
                encab += "<td style='" + estilo[i] + "' onclick='ordenListado(this)'>" + encabezamiento[i] + "</td>";
            }
            else{
                if (tipo_formulario=="exencion_fct" && i==4){
                    encab+="<td style='width:35px'><center>Dpto.</center></td>";
                    encab+="<td style='width:70px' title='Informe del Jefe de Departamento'><center>Informe JD</center></td>";
                    encab+="<td style='"+ estilo[i] + "'>" + encabezamiento[i] + "</td>";
                }
                else {
                    encab += "<td style='"+ estilo[i] + "'>" + encabezamiento[i] + "</td>";
                }
            }
        }
        encab += "<td style='width:90px; text-align: center'>Observaciones</td></tr>";
    }
    else{
        if (orden_campo == "apellidos") encabezamiento[1] += " " + orden_direccion;
        else encabezamiento[campos.indexOf(orden_campo)] += " " + orden_direccion;
        encab = "<tr>";
        if (tipo_formulario != "prematricula") encab += "<td style='width:50px; text-align:center' >Sel.</td>";
        for (i = 0; i < encabezamiento.length; i++) {
            encab += "<td style='" + estilo[i] + "' onclick='ordenListado(this)'>" + encabezamiento[i] + "</td>";
        }
        encab += "<td style='width:90px; text-align: center'>Incidencias</td>";
        if (tipo_formulario != "prematricula") encab += "<td style='width:90px; text-align: center'>Listado</td>";
        if (tipo_formulario.indexOf("matricula")==-1)encab += "<td style='width:110px; text-align: center'>Procesado</td>";
        encab += "</tr>";
    }
    ///////////////////////////////////////////////

    buscar = document.getElementById("busqueda").value;
    if (document.getElementById("check_incidencias").checked) solo_incidencias = 1;
    else solo_incidencias = 0;
    if (tipo_formulario == "matricula_ciclos") {
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            ciclo: document.getElementById("mat_ciclos").value,
            curso_ciclo: document.getElementById("mat_ciclos_curso").value,
            turno: document.getElementById("mat_ciclos_turno").value,
            solo_incidencias: solo_incidencias,
            nuevo_otra_comunidad:(document.getElementById("check_nuevo_otra_com").checked)?"Si":"No"
        }
    } else if (tipo_formulario == "matricula_fpb") {
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            ciclo: document.getElementById("mat_fpb").value,
            curso_ciclo: document.getElementById("mat_fpb_curso").value,
            solo_incidencias: solo_incidencias,
            nuevo_otra_comunidad:(document.getElementById("check_nuevo_otra_com").checked)?"Si":"No"
        }
    } 
    else if(tabla == "mat_eso" || tabla=="mat_bach"){
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            solo_incidencias: solo_incidencias,
            curso_num:curso_num,
            nuevo_otra_comunidad:(document.getElementById("check_nuevo_otra_com").checked)?"Si":"No",
        }
    }
    else if(tabla=="premat_eso"){
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            grupo: grupo,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            solo_incidencias: solo_incidencias,
            curso_num:curso_num
        }
    }
    else if(tabla=="premat_bach"){
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            grupo: grupo,
            modalidad:modalidad,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            solo_incidencias: solo_incidencias,
            curso_num:curso_num
        }
    }
    else if(tabla=="convalidaciones"){
        if (document.getElementById("check_vistas").checked) _v=0;
        else _v=1;
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            vistas:_v
        }
    }
    else if(tabla=="exencion_fct"){
        if (document.getElementById("check_vistas").checked) _v=0;
        else _v=1;
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            departamento:document.getElementById('departamento').value,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            vistas:_v
        }
    }
    else {
        datos = {
            buscar: buscar,
            tabla: tabla,
            curso: document.getElementById('curso').value,
            orden_campo: orden_campo,
            orden_direccion: direccion[orden_direccion],
            solo_incidencias: solo_incidencias,
            curso_num:curso_num
        }
    }
    
    mostrarPantallaEspera();
    $.post("php/secret_listaregsecretaria.php", datos, function(resp) {
        ocultarPantallaEspera();
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "no_tabla" || resp.error == "sin_registros") {
            document.getElementById("div_notabla").style.display = "block";
            document.getElementById("div_tabla").style.display = "none";
            habilitaMenu(false, false);
        }
        else {
            document.getElementById("div_notabla").style.display = "none";
            document.getElementById("div_tabla").style.display = "block";
            
            //encab = "";
            data = "";
            data_array = resp["registros"];
            array_sino=["No","Si"];
            for (i = 0; i < data_array.length; i++) {
                if (tipo_formulario=="convalidaciones"){
                    data += "<tr onclick='verRegistroConvalidaciones(\""+data_array[i]["registro"]+"\")'>";
                    //Datos específicos de cada formulario
                    for (j = 0; j < campos.length; j++) {
                        if (j==0) data += "<td style='" + estilo[j] + "'>" + data_array[i][campos[j]] + "</td>";
                        if (j==1) data += "<td title='"+data_array[i]["registro"]+"' style='" + estilo[j] + "'>" + data_array[i][campos[j]] + "</td>";
                        else if(j==2){
                            data += "<td style='" + estilo[j] + "'>" + data_array[i][campos[j]].substring(8, 10) + '-' + data_array[i][campos[j]].substring(5, 7) + '-' + data_array[i][campos[j]].substring(0, 4) + "</td>";
                            // String de la fecha
                            fechaString = data_array[i][campos[j]]; 

                            // Convertir el string a un objeto Date
                            fecha = new Date(fechaString);

                            // Crear el objeto Date para la fecha límite
                            anno_final_curso=document.getElementById("curso").value.slice(-4);
                            fechaLimite = new Date(anno_final_curso+"-06-15");
                        } 
                        else if (j==3 || j==5 || j==7) data += "<td style='" + estilo[j] + "'>" + array_sino[data_array[i][campos[j]]] + "</td>";
                        else if(j==4){
                            if (data_array[i][campos[3]]==1){
                                //if(data_array[i][campos[j]]==1) data += "<td style='width:70px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); procesadoConvalidaciones(this,\"centro\",\""+data_array[i]["registro"]+"\");'/></center></td>";
                                //else data += "<td style='width:70px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); procesadoConvalidaciones(this,\"centro\",\""+data_array[i]["registro"]+"\");'/></center></td>";    
                                if(data_array[i][campos[j]]==1) data += "<td style='width:70px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                                else data += "<td style='width:70px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";    
                            }
                            else{
                                data += "<td style='width:70px'><center>-</center></td>";     
                            }
                        }
                        else if(j==6){
                            if (data_array[i][campos[5]]==1){
                                //if(data_array[i][campos[j]]==1) data += "<td style='width:70px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); procesadoConvalidaciones(this,\"consejeria\",\""+data_array[i]["registro"]+"\");'/></center></td>";
                                //else data += "<td style='width:70px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); procesadoConvalidaciones(this,\"consejeria\",\""+data_array[i]["registro"]+"\");'/></center></td>";
                                if(data_array[i][campos[j]]==1) data += "<td style='width:70px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                                else data += "<td style='width:70px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                            }
                            else{
                                data += "<td style='width:70px'><center>-</center></td>";     
                            }
                        }
                        else if(j==8){
                            if (data_array[i][campos[7]]==1){
                                //if(data_array[i][campos[j]]==1) data += "<td style='width:70px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); procesadoConvalidaciones(this,\"ministerio\",\""+data_array[i]["registro"]+"\");'/></center></td>";
                                //else data += "<td style='width:70px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); procesadoConvalidaciones(this,\"ministerio\",\""+data_array[i]["registro"]+"\");'/></center></td>";
                                if(data_array[i][campos[j]]==1) data += "<td style='width:70px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                                else data += "<td style='width:70px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                            }
                            else{
                                data += "<td style='width:70px'><center>-</center></td>";     
                            }
                        }
                    }
                    if (fecha < fechaLimite){
                        if (data_array[i]["visto"]==1) data += "<td style='width:70px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' checked onclick='javascript:event.stopPropagation(); formularioProcesado(this);'/></center></td>";
                        else  data += "<td style='width:70px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' onclick='javascript:event.stopPropagation(); formularioProcesado(this);'/></center></td>";
                    }
                    else{
                        if (data_array[i]["visto"]==1) data += "<td style='width:70px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' checked onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                        else  data += "<td style='width:70px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";    
                    }

                    data += "<td style='width:90px'><center>"+array_sino[data_array[i].incidencias]+"</center></td></tr>";

                }
                else if(tipo_formulario=="exencion_fct"){
                    data += "<tr onclick='verRegistroExencionFCT(\""+data_array[i]["registro"]+"\",\""+data_array[i]["rutaInforme"]+"\",\""+data_array[i]["rutaResolucion"]+"\")'>";
                    for (j = 0; j < campos.length; j++) {
                        if(j==2){
                            data += "<td style='" + estilo[j] + "'>" + data_array[i][campos[j]].substring(8, 10) + '-' + data_array[i][campos[j]].substring(5, 7) + '-' + data_array[i][campos[j]].substring(0, 4) + "</td>";
                            // String de la fecha
                            fechaString = data_array[i][campos[j]]; 

                            // Convertir el string a un objeto Date
                            fecha = new Date(fechaString);

                            // Crear el objeto Date para la fecha límite
                            anno_final_curso=document.getElementById("curso").value.slice(-4);
                            fechaLimite = new Date(anno_final_curso+"-06-15");
                        }
                        else if(j==4){//Aquí se hace la columna Informe JD y Resolución juntas 
                            abrDpto="";
                            for (k=0;k<departamentos.length;k++){
                                if (data_array[i]["departamento"]==departamentos[k][0]){
                                    abrDpto=departamentos[k][1];
                                }
                            }
                            data+="<td style='width:35px'><center>"+abrDpto+"</center></td>";
                            dirRegistro=data_array[i]["registro"].slice(17);
                            rutaInforme=data_array[i]["rutaInforme"];
                            rutaResolucion=data_array[i]["rutaResolucion"];                            
                            if (rutaInforme=="" && rutaResolucion==""){
                                data+="<td style='width:70px;'><center>-</center></td>";
                                data += "<td style='" + estilo[j] + "'>-</td>";
                            }
                            else {
                                if (rutaInforme!=""){
                                    data+="<td style='width:70px;'><center><a href='"+rutaInforme+"?q="+Date.now()+"' target='_blank' onclick='event.stopPropagation();'>Ver</a></center></td>";
                                }
                                else{
                                    data += "<td style='width:70px;text-align:center'>-</td>";
                                } 
                                if (rutaResolucion!=""){
                                    data+="<td style='width:70px;'><center><a href='"+rutaResolucion+"?q="+Date.now()+"' target='_blank' onclick='event.stopPropagation();'>"+data_array[i][campos[j]].toUpperCase()+"</a></center></td>";
                                }
                                else{
                                    data += "<td style='" + estilo[j] + "'>-</td>";
                                }
                            }
                        }
                        else{
                            data += "<td style='" + estilo[j] + "'>" + data_array[i][campos[j]] + "</td>";
                        }
                    }
                    //if (fecha < fechaLimite){
                    //    if (data_array[i]["visto"]==1) data += "<td style='width:60px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' checked onclick='javascript:event.stopPropagation(); formularioProcesado(this);'/></center></td>";
                    //    else  data += "<td style='width:60px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' onclick='javascript:event.stopPropagation(); formularioProcesado(this);'/></center></td>";
                    //}
                    //else{
                        //if (data_array[i]["visto"]==1) data += "<td style='width:60px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' checked onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";
                        //else  data += "<td style='width:60px'><center><input type='checkbox' data-registro='"+data_array[i]["registro"]+"' onclick='javascript:event.stopPropagation(); this.checked=!this.checked;'/></center></td>";    
                    //}
                    data += "<td style='width:90px'><center>"+array_sino[data_array[i].incidencias]+"</center></td></tr>"; 
                }
                else{
                    data += "<tr onclick='verRegistro(\""+data_array[i]["registro"]+"\")'>";
                    //Check de selección. si es prematrícula no aparece
                    if (tipo_formulario != "prematricula") {
                        data += "<td style='width:50px;  text-align:center' onclick='javascript:event.stopPropagation();this.children[0].checked=!this.children[0].checked'><input type='checkbox' onclick='javascript: event.stopPropagation();'/></td>";
                    }
    
                    //Datos específicos de cada formulario
                    for (j = 0; j < campos.length; j++) {
                        data += "<td style='" + estilo[j] + "'>" + data_array[i][campos[j]] + "</td>";
                    }
    
                    if (encabezamiento[encabezamiento.length - 1] == "Docs") {
                        data += "<td style='" + estilo[j] + "' onclick='javascript:event.stopPropagation();verDocsMatricula(this.parentNode.children[1].innerHTML,\"<28\")'>Ver</td>";
                    }
    
                    if (encabezamiento[encabezamiento.length - 2] == "Docs") {
                        if (encabezamiento[encabezamiento.length - 1] == ">28") {
                            if (data_array[i]["mayor_28"] == "Si") {
                                data += "<td style='" + estilo[j] + "' onclick='javascript:event.stopPropagation();verDocsMatricula(this.parentNode.children[1].innerHTML,\">28\")'>Ver</td>";
                            } else {
                                data += "<td style='" + estilo[j] + "' onclick='javascript:event.stopPropagation();verDocsMatricula(this.parentNode.children[1].innerHTML,\"<28\")'>Ver</td>";
                            }
                        }
                    }
    
                    if (encabezamiento[encabezamiento.length - 1] == ">28" && tipo_formulario == "matricula_ciclos") {
                        data += "<td style='" + estilo[j] + "'>" + data_array[i]["mayor_28"] + "</td>";
                    }
    
                    //Si hay o no incidencias
                    if (data_array[i].incidencias) data += "<td style='width:90px'><center>Si</center></td>";
                    else data += "<td style='width:90px'><center>No</center></td>";
    
                    //Check de listado. Si es prematrícula no aparece
                    if (tipo_formulario != "prematricula") {
                        if (data_array[i].listado == 1) data += "<td style='width:90px'><center><input type='checkbox' checked onclick='javascript: return false;'/></center></td>";
                        else data += "<td style='width:90px'><center><input type='checkbox' onclick='javascript: return false;'/></center></td>";
                    }
                    //Ckeck de procesado. Si es matrícula o prematrícula no aparece
                    if (tipo_formulario.indexOf("matricula")==-1){
                        if (data_array[i].procesado==1) data += "<td style='width:110px'><center><input type='checkbox' checked onclick='javascript:event.stopPropagation(); formularioProcesado(this);'/></center></td></tr>";
                        else  data += "<td style='width:110px'><center><input type='checkbox' onclick='javascript:event.stopPropagation(); formularioProcesado(this);'/></center></td></tr>";
                    }    
                }
            }
            
            document.getElementById("encabezado_docs").innerHTML = encab;
            document.getElementById("registros_docs").innerHTML = data;
            if (document.getElementById("div_tabla").scrollHeight > document.getElementById("div_tabla").clientHeight) {
                document.getElementById("div_tabla").style.width=document.getElementById("encabezado_docs").offsetWidth+25+"px";
                document.getElementById("div_tabla").style.marginLeft=-5+(document.getElementById("div_listados_formularios").offsetWidth-document.getElementById("encabezado_docs").offsetWidth)/2+"px";
            }
        }
    }, "json");
}


function ordenListado(obj) {
    if (obj.innerHTML == "Docs" || obj.innerHTML == "Incidencias" || obj.innerHTML == "Listado") return;
    if (obj.innerHTML.indexOf("🡅") == -1 && obj.innerHTML.indexOf("🡇") == -1) {
        enc = obj.innerHTML;
        sim_dir = "🡅";
    } else {
        enc = obj.innerHTML.substring(0, obj.innerHTML.length - 3);
        sim_dir = obj.innerHTML.substring(obj.innerHTML.length - 2, obj.innerHTML.length);
        if (sim_dir == "🡅") sim_dir = "🡇";
        else if (sim_dir == "🡇") sim_dir = "🡅";
    }
    campo = campos[encabezamiento.indexOf(obj.innerHTML)];
    if (campo == "nombre") campo = "apellidos";
    _orden_campo = campo;
    _orden_direccion = sim_dir;
    listaRegistros(campo, sim_dir);
}


function procesadoConvalidaciones(obj, organismo, num_registro){
    var proc=0;
    if (obj.checked)proc=1;
    mostrarPantallaEspera();
    $.post("php/secret_convalid_procesado_organismo.php",{registro:num_registro,organismo:organismo,estado_procesado:proc},(resp)=>{
        ocultarPantallaEspera();
        if(resp=="ok") alerta("Estado procesado cambiado correctamente.", "OK");
        else {
            alerta("No se ha podido cambiar el estado del proceso por algún error interno o de la base de datos.", "ERROR");
            obj.checked=!obj.checked;
        }
    });
}

function formularioProcesado(obj){
    if (tipo_formulario!="convalidaciones") num_reg=obj.parentNode.parentNode.parentNode.children[3].innerHTML;
    else num_reg=obj.dataset.registro;
    
    mostrarPantallaEspera();
    p1=Promise.resolve($.post("php/secret_cambia_estado_procesado.php",{registro:num_reg,tabla:tipo_formulario,estado:(obj.checked)?1:0}));
    p2=p1.then((resp)=>{
        if (resp=="server"){
            ocultarPantallaEspera();
            alerta("Error de servidor. Vuelva a intentarlo en otro momento.","ERROR SERVIDOR");
            obj.checked=!obj.checked;
        }
        else if(resp=="errordb"){
            ocultarPantallaEspera();
            alerta("Hay un problema en la base de datos. Vuelva a intentarlo en otro momento.","ERROR DB");
            obj.checked=!obj.checked;
        }
        else if(resp=="ok"){
            return ($.post("php/secret_num_reg_sinrevisar.php", {curso:curso_actual},()=>{},"json"));
        }
    });
    p3=p2.then((resp)=>{
        ocultarPantallaEspera();
        if (resp.error=="ok"){
            generaSelectTipo_form(resp.datos);
        }
        else if(resp.error="server"){
            alerta("Error en base de datos. La aplicación no funcionará correctamente.","ERROR DB");
        }
    });

}


function verRegAdjuntosConvalid(reg){
    _div="";
    mostrarPantallaEspera();
    $.post("php/secret_convalid_adjuntos.php",{registro:reg},(resp2)=>{
        ocultarPantallaEspera();
        if(resp2.error=="server") _div += "<span class='verReg_label'>Hay un problema en sel servidor y no se han podido recuperar los documentos adjuntos.</span>";
        else if(resp2.error=="sin_adjuntos") _div += "<span class='verReg_label'>El alumno no adjuntó documentos a la solicitud.</span>";
        else {
            _div+="<ul id='ul_docs_convalid'>";
            for(i=0;i<resp2.datos.length;i++){
                _div += "<li><a style='color:GREEN;font-size:0.75em' target='_blank' href='"+resp2.datos[i].ruta+"?q="+Date.now()+"'>"+resp2.datos[i].descripcion+"</a>";
                if (resp2.datos[i].subidopor=="secretaria"){
                    _div+="&nbsp&nbsp(<a style='color:RED;font-size:0.75em' href='#' onclick='borraAdjuntos(\"convalidaciones_docs\",\""+resp2.datos[i].ruta+"\",\""+resp2.datos[i].descripcion+"\",\""+reg+"\",1)'>X</a>)";
                }
                _div+="</li>";
            }
            _div+="</ul>";
        }
        document.getElementById("ver_reg_ajuntosConvalid").innerHTML=_div;
    },"json");
}


function verRegAdjuntosExencFCT(reg){
    _div="";
    mostrarPantallaEspera();
    $.post("php/secret_exencion_fct_adjuntos.php",{registro:reg},(resp2)=>{
        ocultarPantallaEspera();
        if(resp2.error=="server") _div += "<span class='verReg_label'>Hay un problema en sel servidor y no se han podido recuperar los documentos adjuntos.</span>";
        else if(resp2.error=="sin_adjuntos") _div += "<span class='verReg_label'>El alumno no adjuntó documentos a la solicitud.</span>";
        else {
            _div+="<ul id='ul_docs_convalid'>";
            for(i=0;i<resp2.datos.length;i++){
                _div += "<li><a style='color:GREEN;font-size:0.75em' target='_blank' href='"+resp2.datos[i].ruta+"?q="+Date.now()+"'>"+resp2.datos[i].descripcion+"</a>";
                if (resp2.datos[i].subidopor=="secretaria"){
                    _div+="&nbsp&nbsp(<a style='color:RED;font-size:0.75em' href='#' onclick='borraAdjuntos(\"exencion_fct_docs\",\""+resp2.datos[i].ruta+"\",\""+resp2.datos[i].descripcion+"\",\""+reg+"\",1)'>X</a>)";
                }
                _div+="</li>";
            }
            _div+="</ul>";
        }
        document.getElementById("ver_reg_ajuntosExencFCT").innerHTML=_div;
    },"json");
}

function verRegistro(registro) {
    ancho = 700;
    form1 = document.getElementById("tipo_form").value;
    var dialogo=generaDivDialog();
    if (form1 == "prematricula") {
        form="premat_"+document.getElementById("curso_pre_mat").value;
    }
    else if (form1 == "matricula"){
        if (document.getElementById("curso").value!="2020-2021" && document.getElementById("curso").value!="2021-2022") {
            if (document.getElementById("curso_mat").value.indexOf("eso")!=-1) form = "mat_eso";
            else form = "mat_bach";
        }
        else form = "mat_" + document.getElementById("curso_mat").value;
    } 
    else if (form1 == "matricula_ciclos") form = "mat_ciclos";
    else if (form1 == "matricula_fpb") form = "mat_fpb";
    else form = form1;

    formulario = form; //esta asignación es necesaria para que funcione en botones, botón Guardar
    botones = "<div style='text-align:right'>";
    botones += "<input type='button' class='textoboton btn btn-success' value='Sin Incidencias' onclick='document.getElementById(\"incidencias_text\").value=\"\"'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Guardar' onclick='actualizaIncidencias(registro,formulario,document.getElementById(\"incidencias_text\").value)'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Cerrar' onclick='javascript:$(\"#"+dialogo+"\").dialog(\"destroy\").remove();'/>";
    botones += "</div>";
    mostrarPantallaEspera();
    $.post("php/secret_recuperaregistro.php", { formulario: form, registro: registro }, function(resp) {
        ocultarPantallaEspera();
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "no_tabla" || resp.error == "sin_registro") alerta("El registro no se encuentra en el servidor.", "No encontrado");
        else if (resp.error == "ok") {
            if (resp.registro.incidencias.trim() != "") incidencia_si = 1;
            else incidencia_si = 0;
            contenido = "";
            if (form == "revision_examen") {
                contenido += "<span class='verReg_label'>ID de Usuario: </span><span class='verReg_campo'>" + resp.registro.id_nif + "</span><br>";
                contenido += "<span class='verReg_label'>Teléfono: </span><span class='verReg_campo'>" + resp.registro.telefono + "</span><br>";
                contenido += "<span class='verReg_label'>Fecha del registro: </span><span class='verReg_campo'>" + resp.registro.fecha_registro + "</span><br>";
                contenido += "<span class='verReg_label'>Nº Registro: </span><span class='verReg_campo'>" + registro + "</span><br>";
                contenido += "<span class='verReg_label'>Solicitante: </span><span class='verReg_campo'>" + resp.registro.nombre + "</span><br>";
                contenido += "<span class='verReg_label'>Tipo de Documento: </span><span class='verReg_campo'>" + resp.registro.tipo_doc + "     </span>";
                contenido += "<span class='verReg_label'>Nº Documento: </span><span class='verReg_campo'>" + resp.registro.numero_doc + "</span><br>";
                if (resp.registro.en_calidad_de != "ALUMNO") {
                    contenido += "<span class='verReg_label'>Representa al solicitante como: </span><span class='verReg_campo'>" + resp.registro.en_calidad_de + "</span><br>";
                    contenido += "<span class='verReg_label'>Nombre del alumno: </span><span class='verReg_campo'>" + resp.registro.del_alumno + "</span><br>";
                }
                contenido += "<span class='verReg_label'>Revisión de examen de la ASIGNATURA: </span><span class='verReg_campo'>" + resp.registro.asignatura + "</span><br>";
                contenido += "<span class='verReg_label'>FECHA del examen: </span><span class='verReg_campo'>" + resp.registro.fecha + "</span><br>";
                contenido += "<span class='verReg_label'>CURSO: </span><span class='verReg_campo'>" + resp.registro.cursa + "</span><br>";
                contenido += "<span class='verReg_label'>Profesor implicado: </span><span class='verReg_campo'>" + resp.registro.profesor + "</span><br>";
                contenido += "<span class='verReg_label'>Departamento: </span><span class='verReg_campo'>" + resp.registro.departamento + "</span><br>";
                contenido += "<span class='verReg_label'>INCIDENCIAS DE LA SOLICITUD: </span><br>";
                contenido += "<textarea id='incidencias_text' style='width:95%' onchange='javascript:actualizar=true;' class='verReg_campo'>" + resp.registro.incidencias + "</textarea>";
                contenido += botones;
                document.getElementById(dialogo).innerHTML = contenido;
            } else if (form == "revision_calificacion") {
                contenido += "<span class='verReg_label'>ID de Usuario: </span><span class='verReg_campo'>" + resp.registro.id_nif + "</span><br>";
                contenido += "<span class='verReg_label'>Teléfono: </span><span class='verReg_campo'>" + resp.registro.telefono + "</span><br>";
                contenido += "<span class='verReg_label'>Fecha del registro: </span><span class='verReg_campo'>" + resp.registro.fecha_registro + "</span><br>";
                contenido += "<span class='verReg_label'>Nº Registro: </span><span class='verReg_campo'>" + registro + "</span><br>";
                contenido += "<span class='verReg_label'>Solicitante: </span><span class='verReg_campo'>" + resp.registro.nombre + "</span><br>";
                contenido += "<span class='verReg_label'>Tipo de Documento: </span><span class='verReg_campo'>" + resp.registro.tipo_doc + "     </span>";
                contenido += "<span class='verReg_label'>Nº Documento: </span><span class='verReg_campo'>" + resp.registro.numero_doc + "</span><br>";
                contenido += "<span class='verReg_label'>Domicilio: </span><span class='verReg_campo'>" + resp.registro.domicilio + "</span><br>";
                contenido += "<span class='verReg_label'>C.P.: </span><span class='verReg_campo'>" + resp.registro.cp + "</span>";
                contenido += "<span class='verReg_label'>Población: </span><span class='verReg_campo'>" + resp.registro.poblacion + "</span><br>";
                contenido += "<span class='verReg_label'>Provincia: </span><span class='verReg_campo'>" + resp.registro.provincia + "</span><br>";
                contenido += "<span class='verReg_label'>Ciclo de Grado: </span><span class='verReg_campo'>" + resp.registro.ciclo_grado + "</span>";
                contenido += "<span class='verReg_label'>Nombre: </span><span class='verReg_campo'>" + resp.registro.ciclo_nombre + "</span><br>";
                contenido += "<span class='verReg_label'>Módulo cursado: </span><span class='verReg_campo'>" + resp.registro.modulo + "</span><br>";
                contenido += "<span class='verReg_label'>Nota obtenida: </span><span class='verReg_campo'>" + resp.registro.nota + "</span><br>";
                contenido += "<span class='verReg_label'>Motivos de la reclamación: </span><br>";
                contenido += "<span class='verReg_campo'>" + resp.registro.motivos + "</span><br>";
                contenido += "<span class='verReg_label'>INCIDENCIAS DE LA SOLICITUD: </span><br>";
                contenido += "<textarea id='incidencias_text' style='width:95%' onchange='javascript:actualizar=true;' class='verReg_campo'>" + resp.registro.incidencias + "</textarea>";
                contenido += botones;
                document.getElementById(dialogo).innerHTML = contenido;
            }  else if (form1 == "prematricula" || form1 == "matricula") {
                if (form1 == "matricula") {
                    if (resp.registro.consolida_premat == "Si") {
                        contenido += "<div style='text-align:center>";
                        contenido += "<span class='verReg_label'>¡¡¡CONSOLIDA PREMATRÍCULA!!!</span>";
                        contenido += "</div>";
                        contenido += "<br>";
                    }
                    contenido += "<span class='verReg_label'>Alumno Nuevo: </span><span class='verReg_campo'>" + resp.registro.al_nuevo + "</span>";
                    if(resp.registro.al_nuevo_otracomunidad!=undefined)contenido += "<span class='verReg_label'>Nuevo de otra comunidad: </span><span class='verReg_campo'>" + resp.registro.al_nuevo_otracomunidad + "</span>";
                    contenido += "<span class='verReg_label' style='margin-left:10px'>Repite: </span><span class='verReg_campo'>" + resp.registro.repite + "</span>";
                    contenido += "<span class='verReg_label' style='margin-left:10px'>Interno: </span><span class='verReg_campo'>" + resp.registro.interno + "</span>";
                    if (form.indexOf("eso") > -1) contenido += "<span class='verReg_label' style='margin-left:10px'>Transporte: </span><span class='verReg_campo'>" + resp.registro.transporte + "</span>";
                    contenido += "<span class='verReg_label' style='margin-left:10px'>Autor. uso fotos: </span><span class='verReg_campo'>" + resp.registro.autoriza_fotos + "</span><br>";
                }
                if (form1 == "matricula") contenido += "<span class='verReg_label'>NIF/NIE: </span><span class='verReg_campo'>" + resp.registro.nif_nie + "</span><br>";
                contenido += "<span class='verReg_label'>Teléfono alumno: </span><span class='verReg_campo'>" + resp.registro.telef_alumno + "</span><br>";
                contenido += "<span class='verReg_label'>Email Alumno: </span><span class='verReg_campo'>" + resp.registro.email_alumno + "</span><br>";
                if (form1 == "matricula") {
                    contenido += "<span class='verReg_label'>Dirección: </span><span class='verReg_campo'>" + resp.registro.direccion + "</span><br>";
                    contenido += "<span class='verReg_label'>CP: </span><span class='verReg_campo'>" + resp.registro.cp + "</span><br>";
                    contenido += "<span class='verReg_label'>Localidad: </span><span class='verReg_campo'>" + resp.registro.localidad + "     </span>";
                    contenido += "<span class='verReg_label'>Provincia: </span><span class='verReg_campo'>" + resp.registro.provincia + "</span><br>";
                }
                contenido += "<span class='verReg_label' style='text-decoration:underline'>DATOS TUTOR/A LEGAL 1</span><br>";
                contenido += "<span class='verReg_label'>Nombre y Apellidos: </span><span class='verReg_campo'>" + resp.registro.tutor1 + "</span><br>";
                //if (form1 == "matricula") contenido += "<span class='verReg_label'>NIF/NIE: </span><span class='verReg_campo'>" + resp.registro.nif_nie_tutor1 + "</span>";
                contenido += "<span class='verReg_label'>Teléfono: </span><span class='verReg_campo'>" + resp.registro.tlf_tutor1 + "</span><br>";
                contenido += "<span class='verReg_label'>Email: </span><span class='verReg_campo'>" + resp.registro.email_tutor1 + "</span><br>";
                contenido += "<span class='verReg_label' style='text-decoration:underline'>DATOS TUTOR/A LEGAL 2</span><br>";
                contenido += "<span class='verReg_label'>Nombre y Apellidos: </span><span class='verReg_campo'>" + resp.registro.tutor2 + "</span>";
                //if (form1 == "matricula") contenido += "<span class='verReg_label'>NIF/NIE: </span><span class='verReg_campo'>" + resp.registro.nif_nie_tutor2 + "</span><br>";
                contenido += "<span class='verReg_label'>Teléfono: </span><span class='verReg_campo'>" + resp.registro.tlf_tutor2 + "</span><br>";
                contenido += "<span class='verReg_label'>Email: </span><span class='verReg_campo'>" + resp.registro.email_tutor2 + "</span><br>";
                if (form.indexOf("premat_">-1)) contenido += "<span class='verReg_label' style='text-decoration:underline'>MATERIAS DE LA PREMATRÍCULA</span><br>";
                if (form == "premat_2eso") {
                    contenido += "<span class='verReg_label'>Programa Ligüístico: </span><span class='verReg_campo'>" + resp.registro.prog_ling + "</span><br>";
                    contenido += "<span class='verReg_label'>Religión/Valores Éticos: </span><span class='verReg_campo'>" + resp.registro.rel_valores_et + "</span><br>";
                    contenido += "<span class='verReg_label'>1ª Lengua Extranjera: </span><span class='verReg_campo'>" + resp["registro"]["1_lengua_extr"] + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 1: </span><span class='verReg_campo'>" + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 2: </span><span class='verReg_campo'>" + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 3: </span><span class='verReg_campo'>" + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 4: </span><span class='verReg_campo'>" + resp.registro.optativa4 + "</span><br>";
                } else if (form == "premat_3eso") {
                    contenido += "<span class='verReg_label'>Programa Ligüístico: </span><span class='verReg_campo'>" + resp.registro.prog_ling + "</span><br>";
                    contenido += "<span class='verReg_label'>Religión/Valores Éticos: </span><span class='verReg_campo'>" + resp.registro.rel_valores_et + "</span><br>";
                    contenido += "<span class='verReg_label'>1ª Lengua Extranjera: </span><span class='verReg_campo'>" + resp["registro"]["1_lengua_extr"] + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 1: </span><span class='verReg_campo'>" + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 2: </span><span class='verReg_campo'>" + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 3: </span><span class='verReg_campo'>" + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 4: </span><span class='verReg_campo'>" + resp.registro.optativa4 + "</span><br>";
                } else if (form == "premat_4eso") {
                    contenido += "<span class='verReg_label'>Programa Ligüístico: </span><span class='verReg_campo'>" + resp.registro.prog_ling + "</span><br>";
                    contenido += "<span class='verReg_label'>1ª Lengua Extranjera: </span><span class='verReg_campo'>" + resp["registro"]["1_lengua_extr"] + "</span><br>";
                    contenido += "<span class='verReg_label'>Religión/Valores Éticos: </span><span class='verReg_campo'>" + resp.registro.rel_valores_et + "</span><br>";
                    contenido += "<span class='verReg_label'>Matemáticas: </span><span class='verReg_campo'>" + resp.registro.matematicas + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 1: </span><span class='verReg_campo'>" + resp.registro.opc_bloque1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 2: </span><span class='verReg_campo'>" + resp.registro.opc_bloque21 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 2: </span><span class='verReg_campo'>" + resp.registro.opc_bloque22 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 2: </span><span class='verReg_campo'>" + resp.registro.opc_bloque23 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 2: </span><span class='verReg_campo'>" + resp.registro.opc_bloque24 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 3: </span><span class='verReg_campo'>" + resp.registro.opc_bloque31 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 3: </span><span class='verReg_campo'>" + resp.registro.opc_bloque32 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 3: </span><span class='verReg_campo'>" + resp.registro.opc_bloque33 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 3: </span><span class='verReg_campo'>" + resp.registro.opc_bloque34 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 3: </span><span class='verReg_campo'>" + resp.registro.opc_bloque35 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opc. Bloque 3: </span><span class='verReg_campo'>" + resp.registro.opc_bloque36 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 1: </span><span class='verReg_campo'>" + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 2: </span><span class='verReg_campo'>" + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 3: </span><span class='verReg_campo'>" + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 4: </span><span class='verReg_campo'>" + resp.registro.optativa4 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 5: </span><span class='verReg_campo'>" + resp.registro.optativa5 + "</span><br>";
                } else if (form == "premat_3esodiv") {
                    contenido += "<span class='verReg_label'>Religión/Valores Éticos: </span><span class='verReg_campo'>" + resp.registro.rel_valores_et + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 1: </span><span class='verReg_campo'>" + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 2: </span><span class='verReg_campo'>" + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 3: </span><span class='verReg_campo'>" + resp.registro.optativa3 + "</span><br>";
                } else if (form == "premat_4esodiv") {
                    contenido += "<span class='verReg_label'>Religión/Valores Éticos: </span><span class='verReg_campo'>" + resp.registro.rel_valores_et + "</span><br>";
                    contenido += "<span class='verReg_label'>Opción 1: </span><span class='verReg_campo'>" + resp.registro.opcion1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opción 2: </span><span class='verReg_campo'>" + resp.registro.opcion2 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opción 3: </span><span class='verReg_campo'>" + resp.registro.opcion3 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opción 4: </span><span class='verReg_campo'>" + resp.registro.opcion4 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opción 5: </span><span class='verReg_campo'>" + resp.registro.opcion5 + "</span><br>";
                    contenido += "<span class='verReg_label'>Opción 6: </span><span class='verReg_campo'>" + resp.registro.opcion6 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 1: </span><span class='verReg_campo'>" + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 2: </span><span class='verReg_campo'>" + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 3: </span><span class='verReg_campo'>" + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 4: </span><span class='verReg_campo'>" + resp.registro.optativa4 + "</span><br>";
                    contenido += "<span class='verReg_label'>Optativa 5: </span><span class='verReg_campo'>" + resp.registro.optativa5 + "</span><br>";
                } else if (form == "premat_1bach_h" || form == "premat_1bach_c") {
                    contenido += "<span class='verReg_label'>Modalidad: </span><span class='verReg_campo'>" + resp.registro.modalidad + "</span><br>";
                    contenido += "<span class='verReg_label'>1ª Lengua Extranjera: </span><span class='verReg_campo'>" + resp.registro.primer_idioma+ "</span><br>";
                    contenido += "<span class='verReg_label'>Religión/Valores Éticos: </span><span class='verReg_campo'>" + resp.registro.rel_valores_et + "</span><br>";
                    contenido += "<span class='verReg_label' style='text-decoration:underline'>OBLIGATORIAS</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.obligatoria1 + "</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.obligatoria2 + "</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.obligatoria3 + "</span><br>";
                    contenido += "<div style='display:flex'>";
                    contenido += "<div style='float:left'>";
                    contenido += "<span class='verReg_label' style='text-decoration:underline'>OPTATIVAS</span><br>";
                    contenido += "<span class='verReg_campo'>1 " + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_campo'>2 " + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_campo'>3 " + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_campo'>4 " + resp.registro.optativa4 + "</span><br>";
                    contenido += "<span class='verReg_campo'>5 " + resp.registro.optativa5 + "</span><br>";
                    contenido += "<span class='verReg_campo'>6 " + resp.registro.optativa6 + "</span><br>";
                    contenido += "<span class='verReg_campo'>6 " + resp.registro.optativa7 + "</span><br>";
                    contenido += "<span class='verReg_campo'>6 " + resp.registro.optativa8 + "</span><br>";
                    contenido += "</div>";
                    contenido += "<div style='float-left;margin-left:15px'>"
                    contenido += "<br>";
                    contenido += "<span class='verReg_campo'>1 " + resp.registro.optativa9 + "</span><br>";
                    contenido += "<span class='verReg_campo'>2 " + resp.registro.optativa10 + "</span><br>";
                    contenido += "<span class='verReg_campo'>3 " + resp.registro.optativa11 + "</span><br>";
                    contenido += "<span class='verReg_campo'>4 " + resp.registro.optativa12 + "</span><br>";
                    contenido += "<span class='verReg_campo'>5 " + resp.registro.optativa13 + "</span><br>";
                    contenido += "<span class='verReg_campo'>6 " + resp.registro.optativa14 + "</span><br>";
                    contenido += "<span class='verReg_campo'>7 " + resp.registro.optativa15 + "</span>";
                    contenido += "</div></div><br>";
                }  else if (form == "premat_2bach_h") {
                    contenido += "<span class='verReg_label'>1ª Lengua Extranjera: </span><span class='verReg_campo'>" + resp.registro.primer_idioma + "</span><br>";
                    contenido += "<span class='verReg_label' style='text-decoration:underline'>MODALIDAD</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.modalidad1 + "</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.modalidad2 + "</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.modalidad3 + "</span><br>";
                    contenido += "<span class='verReg_label' style='text-decoration:underline'>OPTATIVAS</span><br>";
                    contenido += "<div style='display:flex'>";
                    contenido += "<div style='float:left'>";
                    contenido += "<span class='verReg_campo'>1 " + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_campo'>2 " + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_campo'>3 " + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_campo'>4 " + resp.registro.optativa4 + "</span><br>";
                    contenido += "<span class='verReg_campo'>5 " + resp.registro.optativa5 + "</span><br>";
                    contenido += "<span class='verReg_campo'>6 " + resp.registro.optativa6 + "</span><br>";
                    contenido += "<span class='verReg_campo'>7 " + resp.registro.optativa7 + "</span><br>";
                    contenido += "<span class='verReg_campo'>8 " + resp.registro.optativa8 + "</span><br>";
                    contenido += "</div>";
                    contenido += "<div style='float:left;margin-left:15px'>"
                    contenido += "<span class='verReg_campo'> 9 " + resp.registro.optativa9 + "</span><br>";
                    contenido += "<span class='verReg_campo'>10 " + resp.registro.optativa10 + "</span><br>";
                    contenido += "<span class='verReg_campo'>11 " + resp.registro.optativa11 + "</span><br>";
                    contenido += "<span class='verReg_campo'>12 " + resp.registro.optativa12 + "</span><br>";
                    contenido += "<span class='verReg_campo'>13 " + resp.registro.optativa13 + "</span><br>";
                    contenido += "<span class='verReg_campo'>14 " + resp.registro.optativa14 + "</span><br>";
                    contenido += "<span class='verReg_campo'>15 " + resp.registro.optativa15 + "</span><br>";
                    contenido += "<span class='verReg_campo'>16 " + resp.registro.optativa16 + "</span><br>";
                    contenido += "</div></div><br>";
                } else if (form == "premat_2bach_c") {
                    contenido += "<span class='verReg_label'>1ª Lengua Extranjera: </span><span class='verReg_campo'>" + resp.registro.primer_idioma + "</span><br>";
                    contenido += "<span class='verReg_label' style='text-decoration:underline'>MODALIDAD</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.modalidad1 + "</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.modalidad2 + "</span><br>";
                    contenido += "<span class='verReg_campo'>- " + resp.registro.modalidad3 + "</span><br>";
                    contenido += "<span class='verReg_label' style='text-decoration:underline'>OPTATIVAS</span><br>";
                    contenido += "<div style='display:flex'>";
                    contenido += "<div style='float:left'>";
                    contenido += "<span class='verReg_campo'>1 " + resp.registro.optativa1 + "</span><br>";
                    contenido += "<span class='verReg_campo'>2 " + resp.registro.optativa2 + "</span><br>";
                    contenido += "<span class='verReg_campo'>3 " + resp.registro.optativa3 + "</span><br>";
                    contenido += "<span class='verReg_campo'>4 " + resp.registro.optativa4 + "</span><br>";
                    contenido += "<span class='verReg_campo'>5 " + resp.registro.optativa5 + "</span><br>";
                    contenido += "<span class='verReg_campo'>6 " + resp.registro.optativa6 + "</span><br>";
                    contenido += "<span class='verReg_campo'>7 " + resp.registro.optativa7 + "</span><br>";
                    contenido += "<span class='verReg_campo'>8 " + resp.registro.optativa8 + "</span><br>";
                    contenido += "</div>";
                    contenido += "<div style='float:left;margin-left:15px'>"
                    contenido += "<span class='verReg_campo'>10 " + resp.registro.optativa9 + "</span><br>";
                    contenido += "<span class='verReg_campo'>11 " + resp.registro.optativa10 + "</span><br>";
                    contenido += "<span class='verReg_campo'>12 " + resp.registro.optativa11 + "</span><br>";
                    contenido += "<span class='verReg_campo'>13 " + resp.registro.optativa12 + "</span><br>";
                    contenido += "<span class='verReg_campo'>14 " + resp.registro.optativa13 + "</span><br>";
                    contenido += "<span class='verReg_campo'>15 " + resp.registro.optativa14 + "</span><br>";
                    contenido += "<span class='verReg_campo'>16 " + resp.registro.optativa15 + "</span><br>";
                    contenido += "</div></div><br>";
                }
                contenido += "<span class='verReg_label'>INCIDENCIAS DE LA SOLICITUD: </span><br>";
                contenido += "<textarea id='incidencias_text' style='width:95%' onchange='javascript:actualizar=true;' class='verReg_campo'>" + resp.registro.incidencias + "</textarea>";
                contenido += botones;
                document.getElementById(dialogo).innerHTML = contenido;
            } else if (form1 == "matricula_ciclos") {
                contenido += "<span class='verReg_label'>NIF/NIE: </span><span class='verReg_campo'>" + resp.registro.nif_nie + "</span><br>";
                if(resp.registro.al_nuevo_otracomunidad.length>0)contenido += "<span class='verReg_label'>Nuevo de otra comunidad: </span><span class='verReg_campo'>" + resp.registro.al_nuevo_otracomunidad + "</span>";
                contenido += "<span class='verReg_label'>Teléfono alumno: </span><span class='verReg_campo'>" + resp.registro.telefono + "</span><br>";
                contenido += "<span class='verReg_label'>Email Alumno: </span><span class='verReg_campo'>" + resp.registro.email + "</span><br>";
                contenido += "<span class='verReg_label'>Dirección: </span><span class='verReg_campo'>" + resp.registro.direccion + "</span><br>";
                contenido += "<span class='verReg_label'>CP: </span><span class='verReg_campo'>" + resp.registro.cp + "</span><br>";
                contenido += "<span class='verReg_label'>Localidad: </span><span class='verReg_campo'>" + resp.registro.localidad + "     </span>";
                contenido += "<span class='verReg_label'>Provincia: </span><span class='verReg_campo'>" + resp.registro.provincia + "</span><br>";
                contenido += "<span class='verReg_label'>Mayor de edad: </span><span class='verReg_campo'>" + resp.registro.mayor_edad + "</span><br>";
                contenido += "<span class='verReg_label'>Autoriza uso de imágenes: </span><span class='verReg_campo'>" + resp.registro.autoriza_fotos + "</span><br>";
                if (resp.registro.mayor_edad == "No") {
                    contenido += "<span class='verReg_label'>Tutor/a legal que autoriza uso de imágenes: </span><span class='verReg_campo'>" + resp.registro.tutor_autorizaciones + "</span><br>";
                }
                contenido += "<span class='verReg_label'>Fecha de nacimiento: </span><span class='verReg_campo'>" + resp.registro.fecha_nac + "</span><br>";

                if (anno_ini_curso - parseInt(resp.registro.fecha_nac.substr(6, 4)) < 28) {
                    contenido += "<span class='verReg_label'>Menor 28 años (requiere seguro escolar): </span><span class='verReg_campo'>SI</span><br>";
                }
                contenido += "<span class='verReg_label'>INCIDENCIAS DE LA SOLICITUD: </span><br>";
                contenido += "<textarea id='incidencias_text' style='width:95%' onchange='javascript:actualizar=true;' class='verReg_campo'>" + resp.registro.incidencias + "</textarea>";
                contenido += botones;
                document.getElementById(dialogo).innerHTML = contenido;
            } else if (form1 == "matricula_fpb") {
                contenido += "<span class='verReg_label'>NIF/NIE: </span><span class='verReg_campo'>" + resp.registro.nif_nie + "</span><br>";
                if(resp.registro.al_nuevo_otracomunidad.length>0) contenido += "<span class='verReg_label'>Nuevo de otra comunidad: </span><span class='verReg_campo'>" + resp.registro.al_nuevo_otracomunidad + "</span>";
                contenido += "<span class='verReg_label'>Teléfono alumno: </span><span class='verReg_campo'>" + resp.registro.telefono + "</span><br>";
                contenido += "<span class='verReg_label'>Email Alumno: </span><span class='verReg_campo'>" + resp.registro.email + "</span><br>";
                contenido += "<span class='verReg_label'>Dirección: </span><span class='verReg_campo'>" + resp.registro.direccion + "</span><br>";
                contenido += "<span class='verReg_label'>CP: </span><span class='verReg_campo'>" + resp.registro.cp + "</span><br>";
                contenido += "<span class='verReg_label'>Localidad: </span><span class='verReg_campo'>" + resp.registro.localidad + "     </span>";
                contenido += "<span class='verReg_label'>Provincia: </span><span class='verReg_campo'>" + resp.registro.provincia + "</span><br>";
                contenido += "<span class='verReg_label'>Autoriza uso de imágenes: </span><span class='verReg_campo'>" + resp.registro.autoriza_fotos + "</span><br>";
                contenido += "<span class='verReg_label'>Tutor/a legal que autoriza uso de imágenes: </span><span class='verReg_campo'>" + resp.registro.tutor_autorizaciones + "</span><br>";
                contenido += "<span class='verReg_label'>Fecha de nacimiento: </span><span class='verReg_campo'>" + resp.registro.fecha_nac + "</span><br>";
                contenido += "<span class='verReg_label'>INCIDENCIAS DE LA SOLICITUD: </span><br>";
                contenido += "<textarea id='incidencias_text' style='width:95%' onchange='javascript:actualizar=true;' class='verReg_campo'>" + resp.registro.incidencias + "</textarea>";
                contenido += botones;
                document.getElementById(dialogo).innerHTML = contenido;
            }
            $("#"+dialogo).dialog({
                autoOpen: true,
                dialogClass: "no-close",
                modal: true,
                draggable: false,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "VISTA DEL REGISTRO",
                width: ancho,
                position: { my: "center top", at: "center top", of: window }
            });
        }
    }, "json");

}


function verRegistroConvalidaciones(num_registro){
    ancho = 700;
    var dialogo=generaDivDialog();
    formulario="convalidaciones"
    botones = "<div style='text-align:right'>";
    botones += "<input type='button' class='textoboton btn btn-success' value='Sin Incidencias' onclick='document.getElementById(\"incidencias_text\").value=\"\"'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Guardar' onclick='actualizaIncidencias(\""+num_registro+"\",\"convalidaciones\",document.getElementById(\"incidencias_text\").value)'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Cerrar' onclick='javascript:$(\"#"+dialogo+"\").dialog(\"destroy\").remove();'/>";
    botones += "</div>";
    contenido="";
    mostrarPantallaEspera();
    $.post("php/secret_recuperaregistro.php", { formulario: formulario, registro: num_registro }, function(resp) {
        ocultarPantallaEspera();
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "no_tabla" || resp.error == "sin_registro") alerta("El registro no se encuentra en el servidor.", "No encontrado");
        else if (resp.error == "ok") {
            contenido += "<span class='verReg_label'>NIE: </span><span class='verReg_campo'>" + resp.registro.id_nie +"</span><span class='verReg_label' style='margin-left:5px'>NIF: </span><span class='verReg_campo'>" + resp.registro.id_nif +"</span><span class='verReg_label' style='margin-left:5px'>Nº Registro: </span><span class='verReg_campo'>" + num_registro +"</span><br>";
            contenido += "<span class='verReg_label'>Alumno: </span><span class='verReg_campo'>" + resp.registro.apellidos +", "+resp.registro.nombre+ "</span><br>";
            contenido += "<span class='verReg_label'>Teléfono Fijo: </span><span class='verReg_campo'>" + resp.registro.tlf_fijo + "</span><br>";
            contenido += "<span class='verReg_label'>Teléfono Móvil: </span><span class='verReg_campo'>" + resp.registro.tlf_movil + "</span><br>";
            contenido += "<span class='verReg_label'>Email: </span><span class='verReg_campo'>" + resp.registro.email + "</span><br>";
            contenido += "<span class='verReg_label'>Cursa: </span><span class='verReg_campo'>"+resp.registro.curso_ciclo+" de Grado " + resp.registro.grado + " "+resp.registro.ciclo+" "+resp.registro.ley+"</span><br>";
            contenido += "<span class='verReg_label'>Turno: </span><span class='verReg_campo'> " + resp.registro.turno + "</span>";
            contenido += "<span class='verReg_label'>Modalidad: </span><span class='verReg_campo'> " + resp.registro.modalidad + "</span><br>";
            contenido += "<span class='verReg_label'>DOCUMENTOS ADJUNTOS: </span><br>";
            contenido +="<div id='ver_reg_ajuntosConvalid'></div>"
            contenido +="<div class='container' style='margin-top:20px'><div class='row'>";
            //contenido +="<div class='col-2'>";
            //contenido +="<label for='ver_docs_resol' class='verReg_label'>RESOLUCION:</label>";
            //contenido +="<label class='verReg_label'>RESOLUCION:</label>";
            //contenido +="</div><div class='col-3'>";
            contenido +="<div class='col-3'>";
            contenido +="<input type='button' class='textoboton btn btn-success btn-sm' value='Resolver' onclick='verPanelResolver(\""+resp.registro.id_nie+"\",\""+num_registro+"\");'/></div>"
            //contenido +="<div class='col-3'>"
            //contenido +="<input type='button' class='textoboton btn btn-success' value='Adjuntar Resolución' onclick='document.getElementById(\"ver_reg_resolucion\").click()'/></div>";
            //contenido +="<div class='col-2'>"
            contenido +="<input type='button' class='textoboton btn btn-success btn-sm' value='Adjuntar Documento' onclick='adjuntaDocAdicional(\""+resp.registro.id_nie+"\",\""+num_registro+"\")'/>";
            contenido += "</div></div>";
            //contenido +="<input type='file' id='ver_reg_resolucion' multiple='false' accept='application/pdf' style='position:absolute;left:-9999px' onchange='adjuntaResolucion(\""+resp.registro.id_nie+"\",\""+num_registro+"\",this)'/>";
            contenido += "<br><span class='verReg_label'>OBSERVACIONES/ESTADO DEL TRÁMITE: </span><br>";
            contenido += "<textarea id='incidencias_text' style='width:100%' onchange='javascript:actualizar=true;' class='verReg_campo form-control'>" + resp.registro.incidencias + "</textarea><br>";
            contenido += botones;
            document.getElementById(dialogo).innerHTML = contenido;
            //document.getElementById("ver_docs_resol").value=resp.registro.resolucion;
            verRegAdjuntosConvalid(num_registro);

            $("#"+dialogo).dialog({
                autoOpen: true,
                dialogClass: "no-close",
                modal: true,
                draggable: false,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "VISTA DEL REGISTRO",
                width: ancho,
                position: { my: "center top", at: "center top", of: window }
            });
        }
    }, "json");
}

function verRegistroExencionFCT(num_registro,rutaInforme,rutaResolucion){
    ancho = 700;
    formulario="exencion_fct"
    var dialogo=generaDivDialog();
    botones = "<div style='text-align:right'>";
    botones += "<input type='button' class='textoboton btn btn-success' value='Sin Incidencias' onclick='document.getElementById(\"incidencias_text\").value=\"\"'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Guardar' onclick='actualizaIncidencias(\""+num_registro+"\",\"exencion_fct\",document.getElementById(\"incidencias_text\").value)'/>";
    botones += "<input style='margin-left:5px' type='button' class='textoboton btn btn-success' value='Cerrar' onclick='javascript:$(\"#"+dialogo+"\").dialog(\"destroy\");'/>";
    botones += "</div>";
    contenido="";
    mostrarPantallaEspera();
    $.post("php/secret_recuperaregistro.php", { formulario: formulario, registro: num_registro }, function(resp) {
        ocultarPantallaEspera();
        if (resp.error == "server") alerta("Error en el servidor. Inténtalo más tarde.", "Error de servidor");
        else if (resp.error == "no_tabla" || resp.error == "sin_registro") alerta("El registro no se encuentra en el servidor.", "No encontrado");
        else if (resp.error == "ok") {
            contenido += "<span class='verReg_label'>NIE: </span><span class='verReg_campo'>" + resp.registro.id_nie +"</span><span class='verReg_label' style='margin-left:5px'>NIF: </span><span class='verReg_campo'>" + resp.registro.id_nif +"</span><span class='verReg_label' style='margin-left:5px'>Nº Registro: </span><span class='verReg_campo'>" + num_registro +"</span><br>";
            contenido += "<span class='verReg_label'>Alumno: </span><span class='verReg_campo'>" + resp.registro.apellidos +", "+resp.registro.nombre+ "</span><br>";
            contenido += "<span class='verReg_label'>Cursa: </span><span class='verReg_campo'>"+resp.registro.curso_ciclo+" de Grado " + resp.registro.grado + " "+resp.registro.ciclo+"</span><br>";
            contenido += "<span class='verReg_label'>DOCUMENTOS ADJUNTOS: </span><br>";
            contenido +="<div id='ver_reg_ajuntosExencFCT'></div>"
            contenido +="<div class='container' style='margin-top:20px'><div class='row'>";
            contenido +="<div class='col-3'>";
            contenido +="<input type='button' class='textoboton btn btn-success' value='Adjuntar Documento' onclick='adjuntaDocAdicionalExencFCT(\""+resp.registro.id_nie+"\",\""+num_registro+"\")'/>";
            contenido += "</div>";
            if (rutaInforme.length>0) {
                contenido +="<div class='col-3'>";
                contenido +="<input type='button' class='textoboton btn btn-success' value='Generar Resolución' onclick='resolucionExencionFCT(\""+num_registro+"\",\""+dialogo+"\")'/>";
                contenido +="</div>";
            }
            if(rutaResolucion.length>0 || rutaInforme.length>0){
                contenido +="<div class='col'>";
                contenido += "<input type='button' class='textoboton btn btn-danger' value='Eliminar Informe del JD y Resolución' title='Esta acción pondrá el estado del registro a NO PROCESADO para que el Jefe de Departamento rehaga el informe.' onclick='invalidaInformeJDExencionFCT(\""+num_registro+"\")'/>";
                contenido +="</div>";
            }
            contenido += "</div>";
            contenido += "<br><span class='verReg_label'>OBSERVACIONES/ESTADO DEL TRÁMITE: </span><br>";
            contenido += "<textarea id='incidencias_text' style='width:100%' onchange='javascript:actualizar=true;' class='verReg_campo form-control'>" + resp.registro.incidencias + "</textarea><br>";
            contenido += botones;
            document.getElementById(dialogo).innerHTML = contenido;
            verRegAdjuntosExencFCT(num_registro);

            $("#"+dialogo).dialog({
                autoOpen: true,
                dialogClass: "no-close",
                modal: true,
                draggable: false,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "VISTA DEL REGISTRO",
                width: ancho,
                position: { my: "center top", at: "center top", of: window }
            });
        }
    }, "json");
}

function verPanelResolver(id_nie,registro){
    ancho=1000;
    salir=false;
    var dialogo=generaDivDialog();
    mostrarPantallaEspera();
    $.post("php/secret_convalid_modulos.php",{registro:registro},(resp)=>{
        ocultarPantallaEspera();
        if (resp["error"]=="ok"){
            panel=document.getElementById(dialogo);
            cont="<form id='form_relacion_modulos_convalid'><input type='hidden' name='registro' value='"+registro+"'/><input type='hidden' name='genera_resolucion' id='genera_resolucion' value=''/><div class='container'><div class='form-group form-row'>";
            cont+="<div class='col-5'><label>Módulo</label></div>";
            cont+="<div class='col-2'><label>Estado</label></div>";
            cont+="<div class='col-5'><label>Motivo No Favorable/No Procede</label></div>";
            cont+="</div>";
            for(i=0;i<resp.datos.length;i++){
                cont+="<div class='form-group form-row'>";
                cont+="<div class='col-5'><input type='text' name='modulo_convalid[]' style='font-size:0.5em' class='form-control' value='"+resp.datos[i].modulo+"'  readonly/></div>";
                cont+="<div class='col-2'><select name='estado_convalid[]' style='font-size:0.5em' class='form-control'/>";
                cont+="<option value=''>Seleccione uno</option>";
                if(resp.datos[i].resolucion=="FAVORABLE") cont+="<option value='FAVORABLE' selected>FAVORABLE</option>";
                else cont+="<option value='FAVORABLE'>FAVORABLE</option>";
                if(resp.datos[i].resolucion=="NO FAVORABLE")cont+="<option value='NO FAVORABLE' selected>NO FAVORABLE</option>";
                else  cont+="<option value='NO FAVORABLE'>NO FAVORABLE</option>";
                if(resp.datos[i].resolucion=="NO PROCEDE")cont+="<option value='NO PROCEDE' selected>NO PROCEDE</option>";
                else  cont+="<option value='NO PROCEDE'>NO PROCEDE</option>";
                if(resp.datos[i].resolucion=="CONSEJERIA")cont+="<option value='CONSEJERIA' selected>CONSEJERIA</option>";
                else  cont+="<option value='CONSEJERIA'>CONSEJERIA</option>";   
                if(resp.datos[i].resolucion=="MINISTERIO")cont+="<option value='MINISTERIO' selected>MINISTERIO</option>";
                else  cont+="<option value='MINISTERIO'>MINISTERIO</option>";  
                cont+="</select></div>";
                cont+="<div class='col-5'><input type='text' name='motivo_no_fav_convalid[]' style='font-size:0.5em' class='form-control' value='"+resp.datos[i].motivo_no_favorable+"'/></div>";
                cont+="</div>";
            }
            cont+="</div></form>";
            document.getElementById(dialogo).innerHTML=cont;
            $("#"+dialogo).dialog({
                autoOpen: true,
                dialogClass: "no-close",
                modal: true,
                draggable: false,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "RESOLUCIÓN CONVALIDACIÓN MÓDULOS",
                width: ancho,
                position: { my: "center top", at: "center top", of: window },
                buttons:[
                    {
                        class: "btn btn-success textoboton btn-sm",
                        text:"Resolver",
                        click:function(){
                            mostrarPantallaEspera();
                            document.getElementById("genera_resolucion").value=1;
                            $.post("php/secret_convalid_estado_resol.php",$("#form_relacion_modulos_convalid").serialize(),(resp)=>{
                                ocultarPantallaEspera();
                                if (resp=="server") alerta("Error en el servidor. No se puede resolver la convalidación","ERROR EN SERVIDOR");
                                else if(resp=="error_db") alerta("Error en base de datos. No se puede resolver la convalidación","ERROR DB");
                                else if(resp=="ok"){
                                    alerta("Proceso terminado","OK");
                                }
                                else if(resp=="error_db_conval") alerta("No se han grabado los datos de resolución de los módulos poruqe no se ha podido actualizar el estado en la tabla convalidaciones.","ERROR TABLA");
                                else if(resp=="no_datospdf") alerta("No se puede generar la notificación para el alumno. Fallo al acceder a los datos de la solicitud o hay un registro duplicado. Revise los datos de la tabla en este último caso.","ERROR DB");
                                else if(resp=="ok_ministerio") alerta("No se genera notificación para el alumno. Resuelve el MINISTERIO.","RESUELVE MINISTERIO");
                                else if(resp=="ok_consejeria") alerta("No se genera notificación para el alumno. Resuelve CONSEJERIA.","RESUELVE CONSEJERIA");
                                else if(resp=="ok_consejeria_ministerio") alerta("No se genera notificación para el alumno. Resuelve el MINISTERIO y CONSEJERIA.","RESUELVE MINISTERIO Y CONSEJERIA");
                                else if(resp=="elementos_sin_resolver") alerta("No se habían resuelto todos los módulos. Se ha cambiado el estado de los que sí lo estaban.","RESOLUCIÓN PARCIAL");
                                listaRegistros(_orden_campo, _orden_direccion);
                                $(this).dialog("destroy").remove();
                            });
                        }
                    },
                    {
                        class: "btn btn-success textoboton btn-sm",
                        text:"Grabar Datos",
                        click:function(){
                            mostrarPantallaEspera();
                            document.getElementById("genera_resolucion").value=0;
                            $.post("php/secret_convalid_estado_resol.php",$("#form_relacion_modulos_convalid").serialize(),(resp)=>{
                                ocultarPantallaEspera();
                                if (resp=="server") alerta("Error en el servidor. No se puede resolver la convalidación","ERROR EN SERVIDOR");
                                else if(resp=="error_db") alerta("Error en base de datos. No se puede resolver la convalidación","ERROR DB");
                                else if(resp=="ok"){
                                    alerta("Proceso terminado","OK");
                                }
                                else if(resp=="error_db_conval") alerta("No se han grabado los datos de resolución de los módulos poruqe no se ha podido actualizar el estado en la tabla convalidaciones.","ERROR TABLA");
                                else if(resp=="no_datospdf") alerta("No se puede generar la notificación para el alumno. Fallo al acceder a los datos de la solicitud o hay un registro duplicado. Revise los datos de la tabla en este último caso.","ERROR DB");
                                else if(resp=="ok_ministerio") alerta("No se genera notificación para el alumno. Resuelve el MINISTERIO.","RESUELVE MINISTERIO");
                                else if(resp=="ok_consejeria") alerta("No se genera notificación para el alumno. Resuelve CONSEJERIA.","RESUELVE CONSEJERIA");
                                else if(resp=="ok_consejeria_ministerio") alerta("No se genera notificación para el alumno. Resuelve el MINISTERIO y CONSEJERIA.","RESUELVE MINISTERIO Y CONSEJERIA");
                                else if(resp=="elementos_sin_resolver") alerta("No se habían resuelto todos los módulos. Se ha cambiado el estado de los que sí lo estaban.","RESOLUCIÓN PARCIAL");
                                listaRegistros(_orden_campo, _orden_direccion);
                                $(this).dialog("destroy").remove();
                            });
                        }
                    },
                    {
                        class: "btn btn-success textoboton btn-sm",
                        text:"Cancelar",
                        click:function(){
                            listaRegistros(_orden_campo, _orden_direccion);
                            $(this).dialog("destroy").remove();
                        }
                    }
                ]
            });
        }
        else if(resp["error"]=="sin_modulos"){
            alerta("No hay módulos que convalidar.","SIN MÓDULOS");
        } 
        else{
            alerta("Error en servidor o base de datos.","ERROR");
        } 
        
    },"json");

}


function actualizaIncidencias(registro, form, incidencias) {
    mostrarPantallaEspera();
    $.post("php/secret_actualizaIncidencias.php", { registro: registro, formulario: form, incidencias: incidencias, aviso_incidencia_solventada: incidencia_si }, function(resp) {
        ocultarPantallaEspera();
        if (resp == "ok"){
            alerta("Registro actualizado", "OK");
            listaRegistros(_orden_campo, _orden_direccion);
        } 
        else if (resp == "inhabilitado") alerta("El usuario está INHABILITADO y no se le enviará ,ninguna notificación.", "INHABILITADO - SIN NOTIFICACIÓN");
        else if (resp == "server") alerta("Ha habido un error en el servidor. Inténtalo más tarde.<br>" + resp, "ERROR EN SERVIDOR");
        else alerta("No se ha podido actualizar el registro.<br>" + resp, "ERROR");
    });
}

function panelNuevoUsuario() { 
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "div_nuevo_registro","NUEVAS ALTAS",550,2000)
    .then ((dialogo)=>{
        ocultarPantallaEspera();
        document.getElementById('nr_password').value=generaPass();
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}



function altaUsuario() {
    
    if (document.getElementById("form_alta_usuario").checkValidity()) {
        mostrarPantallaEspera();
        $.post("php/secret_nuevousuario.php", $("#form_alta_usuario").serialize(), function(resp) {
            ocultarPantallaEspera();
            if (resp == "server" || resp == "fallo_cambio") {
                alerta("Ha habido un fallo en el servidor y no se ha podido registrar el nuevo usuario. Inténtelo más tarde.", "ERROR DE SERVIDOR");
            } else if (resp == "ok") {
                alerta("Nuevo usuario creado con éxito.", "Alta OK");
            } else if (resp == "usuario") {
                mostrarPantallaEspera();
                cargaHTML("html/secretaria.htm", "div_nie_registrado","NIE REGISTRADO",550,500)
                .then((dialogo)=>{
                    ocultarPantallaEspera();
                    $("[data-alta='usuario'").css("display", "inherit");
                    $("[data-alta='registrado'").css("display", "none");
                })
                .catch (error=>{
                    ocultarPantallaEspera();
                    var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
                    alerta(msg,"ERROR DE CARGA");
                });
            } else if (resp == "registrado") {
                mostrarPantallaEspera();
                cargaHTML("html/secretaria.htm", "div_nie_registrado","NIE REGISTRADO",550,500)
                .then((dialogo)=>{
                    ocultarPantallaEspera();
                    $("[data-alta='usuario'").css("display", "none");
                    $("[data-alta='registrado'").css("display", "inherit");
                })
                .catch (error=>{
                    ocultarPantallaEspera();
                    var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
                    alerta(msg,"ERROR DE CARGA");
                });
            } else if (resp == "email") {
                alerta("Nuevo usuario creado con éxito. Se han enviado sus credenciales por el correo indicado.", "Alta y envío OK");
            } else if (resp.indexOf("envio") == 0) {
                alerta("Nuevo usuario creado con éxito, pero no ha sido posible enviar las credenciales por correo electrónico.", "Alta OK - Error en envío");
            }
        });
    } else document.getElementById("form_alta_usuario").classList.add("was-validated");
}

function reasignarPassword() {
    mostrarPantallaEspera();
    $.post("php/secret_cambiopassword.php", $("#form_alta_usuario").serialize(), function(resp) {
        ocultarPantallaEspera();
        if (resp == "server" || resp == "fallo_alta") {
            alerta("Ha habido un fallo en el servidor y no se ha podido cambiar la contraseña. Inténtelo más tarde.", "ERROR DE SERVIDOR");
        } else if (resp == "ok") {
            alerta("Contraseña cambiada con éxito.", "Alta OK");
        } else if (resp == "email") {
            alerta("Contraseña cambiada con éxito. Se han enviado sus credenciales por el correo indicado.", "Alta y envío OK");
        } else if (resp == "envio") {
            alerta("Contraseña cambiada con éxito, pero no ha sido posible enviar las credenciales por correo electrónico.", "Alta OK - Error en envío");
        }
    });
}

function seleccionaRegistros(valor) {
    tabla = document.getElementById("registros_docs");
    for (i = 0; i < tabla.children[0].children.length; i++) {
        if (valor == "todo") {
            tabla.children[0].children[i].children[0].children[0].checked = true;
        } else if (valor == "ninguno") {
            tabla.children[0].children[i].children[0].children[0].checked = false;
        } else if (valor == "invertir") {
            tabla.children[0].children[i].children[0].children[0].checked = !tabla.children[0].children[i].children[0].children[0].checked;
        }
    }

}

function habilitaMenu(m2, m3) {
    if (m2) $("#menu2").removeClass("disabled");
    else if (!m2) $("#menu2").addClass("disabled");
    if (m3) $("#menu3").removeClass("disabled");
    else if (!m3) $("#menu3").addClass("disabled");
}


function registrosAPdf(tipo_listado) {
    mostrarPantallaEspera();
    cargaHTML("html/secretaria_usu.htm", "formulario_descargar_solicitudes","",0,0)
    .then ((dialogo)=>{
            ocultarPantallaEspera();
            //tipo_listado=>seleccionadas, no listadas, listadas, todas, ''
            if (tipo_listado == '') {
                if (document.getElementById("listadas_seleccionadas").checked) tipo_listado = "seleccionadas";
                else if (document.getElementById("listadas_si").checked) tipo_listado = "listadas";
                else if (document.getElementById("listadas_no").checked) tipo_listado = "no listadas";
                else if (document.getElementById("listadas_todas").checked) tipo_listado = "todas";

                if (document.getElementById("tipo_form").value == "matricula") {
                    if (document.getElementById("cons_si").checked) document.getElementById("mat_consolidadas").value = "consolidadas";
                    else if (document.getElementById("cons_no").checked) document.getElementById("mat_consolidadas").value = "no consolidadas";
                    else if (document.getElementById("cons_todas").checked) document.getElementById("mat_consolidadas").value = "todas";
                }

                document.getElementById("mat_curso").value = document.getElementById("curso_mat").value;
                if (document.getElementById("tipo_form").value == "matricula_ciclos") {
                    document.getElementById("mat_curso").value = "ciclos";
                    document.getElementById("ciclo").value = document.getElementById("mat_ciclos").value;
                    document.getElementById("curso_ciclo").value = document.getElementById("mat_ciclos_curso").value;
                    document.getElementById("turno").value = document.getElementById("mat_ciclos_turno").value;
                    ciclo_seleccionado = document.getElementById("mat_ciclos").options[document.getElementById("mat_ciclos").selectedIndex].text;
                    document.getElementById("grado").value = ciclo_seleccionado.substr(0, 2);
                } else if (document.getElementById("tipo_form").value == "matricula_fpb") {
                    document.getElementById("mat_curso").value = "fpb";
                    document.getElementById("ciclo").value = document.getElementById("mat_fpb").value;
                    document.getElementById("curso_ciclo").value = document.getElementById("mat_fpb_curso").value;
                }
                $('#div_listadoMatriculas').dialog('close');
            }
            document.getElementById('formulario').value = document.getElementById('tipo_form').value;
            document.getElementById("tipo_listado").value = tipo_listado;
            document.getElementById("curso_listado").value = document.getElementById("curso").value;
            encab = document.getElementById("encabezado_docs").rows[0];

            for (i = 0; i < encab.cells.length; i++) {
                if (encab.cells[i].innerHTML.indexOf("🡅") != -1) {
                    document.getElementById("orden_campo").value = campos[encabezamiento.indexOf(encab.cells[i].innerHTML)];
                    document.getElementById("orden_direccion").value = "ASC";
                    document.getElementById("orden_texto").value = encab.cells[i].innerHTML.substring(0, encab.cells[i].innerHTML.length - 3) + " (ASCENDENTE)";
                    break;
                } else if (encab.cells[i].innerHTML.indexOf("🡇") != -1) {
                    document.getElementById("orden_campo").value = campos[encabezamiento.indexOf(encab.cells[i].innerHTML)];
                    document.getElementById("orden_direccion").value = "DESC";
                    document.getElementById("orden_texto").value = encab.cells[i].innerHTML.substring(0, encab.cells[i].innerHTML.length - 3) + " (DESCENDENTE)";
                    break;
                }
            }

            registros = new Array();
            if (tipo_listado == "seleccionadas") {
                for (i = 0; i < encabezamiento.length; i++) {
                    if (encabezamiento[i].indexOf("Nº Registro") != -1) {
                        posicion_campo_registro = i;
                        break;
                    }
                }
                tab = document.getElementById("registros_docs");
                for (i = 0; i < tab.rows.length; i++) {
                    if (tab.rows[i].cells[0].children[0].checked) registros.push(tab.rows[i].cells[posicion_campo_registro + 1].innerHTML);
                }
            }

            document.getElementById("registros").value = JSON.stringify(registros);
            document.getElementById("descarga_sol").submit();
            boton_refrescar = "<center><input style='font-size:1.5em !important' type=\"button\" class=\"btn btn-success textoboton\" value=\"Refrescar Listado\" onclick=\"";
            boton_refrescar += "listaRegistros(_orden_campo,_orden_direccion);\">";
            boton_refrescar += "</center>";
            document.getElementById("registros_docs").innerHTML = boton_refrescar;
            
        }
    )
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}


function cierrasesion() {
    $.post("php/logout.php", {}, function(resp) {
        open("index.php?q=" + Date.now().toString(), "_self");
    });
}

function cambiaEstadoPrematricula(obj, nivel) {
    mostrarPantallaEspera();
    $.post('php/secret_prematricula.php', { matricula: nivel, peticion: 'write', estado: obj.checked }, function(resp) {
        ocultarPantallaEspera();
    });
}

function cambiaEstadoMatricula(obj, nivel) {
    if (obj.checked) estado=1;
    else estado=0;
    mostrarPantallaEspera();
    $.post('php/secret_matricula.php', { matricula: nivel, peticion: 'write', estado: estado }, function(resp) {
        ocultarPantallaEspera();
    });
}


function subeExcel(obj) {
    if (obj.files[0].type != "text/csv") {
        alerta("El fichero seleccionado no es del tipo CSV.", "FORMATO ARCHIVO ERRÓNEO");
        return;
    }
    datos = new FormData();
    datos.append("csv", obj.files[0]);
    mostrarPantallaEspera();
    $.ajax({
            url: "php/secret_csv_nuevosusus.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            ocultarPantallaEspera();
            if (typeof parseInt(resp) == "number" && parseInt(resp) >= 0) {
                window.location.assign("php/excel/" + obj.files[0].name);
                if (parseInt(resp) == 0) alerta("Usuarios dados de alta correctamente", "Alta OK");
                else alerta("Han habido errores al dar de alta a " + resp + " usuarios. Revise el fichero CSV descargado.", "Error Altas");
            } else if (resp == "archivo") alerta("Ha habido un error al subir el archivo.", "Error carga");
            else if (resp == "almacenar") alerta("Ha habido un error al copiar el archivo.", "Error copia");
            else if (resp == "abrir") alerta("El fichero se ha subido pero no puede ser abierto.", "Error FOPEN");
            else if (resp == "noexiste") alerta("El fichero no existe.", "Error archivo");
            else if (resp == "server") alerta("Error de conexión con la base de datos. No se pueden procesar los registros del fichero.", "Error base de datos");
            else if (resp = "formato_archivo") alerta("Formato de archivo inválido.", "ERROR FORMATO");
            obj.value = null;
        });

}

function descargaCSVpremat() {
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_prematricula.php",
            datos:{
                premat_csv:"premat_" + document.getElementById("curso_pre_mat").value,
                curso_csv:document.getElementById("curso").value,
            }
        }
    );
}


function verDocsMatricula(id, edad) {
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "div_docs_matricula","DOCUMENTOS DE LA MATRÍCULA",800,2000,"center center","center center")
    .then ((dialogo)=>{
            _curso = document.getElementById("curso").value;
            if (typeof edad === 'undefined') edad=0;
            d1 = Promise.resolve($.post("php/secret_compruebafoto.php", { url: "../docs/" + id + "/seguro/" + _curso + "/" + id }));
            d2 = d1.then((resp1) => {
                if (resp1 != "no_existe") {
                    _dir="docs/" + id + "/seguro/" + _curso + "/" + id + resp1 + "?q=" + Date.now();
                    document.getElementById("doc_mat_seguro").src = _dir;
                    document.getElementById("seguro_link").setAttribute("href", _dir);
                    document.getElementById("seguro_link").setAttribute("target", "_blank");
                } else {
                    if (edad == "<28") {
                        document.getElementById("doc_mat_seguro").src = "recursos/no_documento.jpg";
                        document.getElementById("seguro_link").setAttribute("href", "#");
                        document.getElementById("seguro_link").setAttribute("target", "_self");
                    } else if (edad == ">28") {
                        document.getElementById("doc_mat_seguro").src = "recursos/no_seguro.jpg";
                        document.getElementById("seguro_link").setAttribute("href", "#");
                        document.getElementById("seguro_link").setAttribute("target", "_self");
                    }
                }
                return $.post("php/secret_compruebafoto.php", { url: "../docs/" + id + "/dni/" + id + "-A" });
            });
            d3 = d2.then((resp2) => {
                if (resp2 != "no_existe") {
                    _dir="docs/" + id + "/dni/" + id + "-A"+resp2+ "?q=" + Date.now();
                    document.getElementById("doc_dni_a").src = _dir;
                    document.getElementById("dni_a_link").setAttribute("href", _dir);
                    document.getElementById("dni_a_link").setAttribute("target", "_blank");
                } else {
                    document.getElementById("doc_dni_a").src = "recursos/no_documento.jpg";
                    document.getElementById("dni_a_link").setAttribute("href", "#");
                    document.getElementById("dni_a_link").setAttribute("target", "_self");
                }
                return $.post("php/secret_compruebafoto.php", { url: "../docs/" + id + "/dni/" + id + "-R" });
            });
            d4 = d3.then((resp3) => {
                if (resp3 != "no_existe") {
                    _dir="docs/" + id + "/dni/" + id + "-R"+resp3 + "?q=" + Date.now();
                    document.getElementById("doc_dni_r").src = _dir ;
                    document.getElementById("dni_r_link").setAttribute("href", _dir);
                    document.getElementById("dni_r_link").setAttribute("target", "_blank");
                }else {
                    document.getElementById("doc_dni_r").src = "recursos/no_documento.jpg";
                    document.getElementById("dni_r_link").setAttribute("href", "#");
                    document.getElementById("dni_r_link").setAttribute("target", "_self");
                }
                return $.post("php/secret_compruebafoto.php", { url: "../docs/fotos/" + id });
            });
            d4.then((resp4) => {
                ocultarPantallaEspera();
                if (resp4 != "no_existe") {
                    _dir="docs/fotos/" + id + resp4 + "?q=" + Date.now();
                    document.getElementById("doc_mat_foto").src = _dir;
                    document.getElementById("foto_link").setAttribute("href", _dir);
                    document.getElementById("foto_link").setAttribute("target", "_blank");
                }  else {
                    document.getElementById("doc_mat_foto").src = "recursos/no_foto.jpg";
                    document.getElementById("foto_link").setAttribute("href", "#");
                    document.getElementById("foto_link").setAttribute("target", "_self");
                }
               
            });            

        }
    )
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}

function descargaCSVtransporte() {
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_transporte.php",
            datos:{
                curso_csv_transporte:document.getElementById("curso").value
            }
        });
}

function listaMatriculas() {
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "div_listadoMatriculas","MATRÍCULAS: SELECCIÓN TIPO LISTADO",900,2000)
    .then ((dialogo)=>{
            ocultarPantallaEspera();
            if (document.getElementById("tipo_form").value == "matricula") {
                document.getElementById("div_consolidadas").style.display = "inherit";
            } else if (document.getElementById("tipo_form").value == "matricula_ciclos") {
                document.getElementById("div_consolidadas").style.display = "none";
            } else if (document.getElementById("tipo_form").value == "matricula_fpb") {
                document.getElementById("div_consolidadas").style.display = "none";
            }
        }
    )
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });

}


function descargaCSVmatriculas() {
    document.getElementById("mat_csv").value = "mat_" + document.getElementById("curso_mat").value;
    document.getElementById("curso_csv_mat").value = document.getElementById("curso").value;
    document.getElementById("descarga_csv_matricula").submit();
}


function descargaFotos() {
    enviarFormularioSubmit(
    {
        url:"php/secret_descargafotos.php",
        datos:{
            usuario:"secretaria"
        }
    });
}


function verListaUsuarios() {
    $("#doc_reg_tab").addClass("d-none");
    $("#usu_reg_tab").removeClass("d-none");
}


function listadoSeguroEscolarCiclos() {
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_segurociclos.php",
            datos:{
                curso_csv_seguro:_curso
            }
        });

}

function listadoNumSS(){
    enviarFormularioSubmit({url:"php/secret_csv_fct_num_ss.php"});
}

//Oculta los cursos que no deben aparecer en función del año por los cambios de la ley
function ocultaCursosDesplegable() {
    if (document.getElementById("curso").value == "2020-2021") {
        $("[itemprop='2020-2021']").show();
        $("[itemprop='2021-2022']").hide();
        $("[itemprop='2022-2023']").hide();
    }
    else if(document.getElementById("curso").value == "2021-2022"){
        if (document.getElementById("tipo_form").value=="matricula") $("[itemprop='2020-2021']").show();
        else $("[itemprop='2020-2021']").hide();
        $("[itemprop='2021-2022']").show();
        $("[itemprop='2022-2023']").hide();
    }
    else {
        $("[itemprop='2020-2021']").hide();
        $("[itemprop='2021-2022']").show();
        $("[itemprop='2022-2023']").show();
    }
}

function subeMatDelphos(obj){
    if (obj.files[0].type != "text/csv") {
        alerta("El fichero seleccionado no es del tipo CSV.", "FORMATO ARCHIVO ERRÓNEO");
        return;
    }
    if(document.getElementById('delimitador').value.trim()=="")document.getElementById('delimitador').value=",";
    if(document.getElementById('acotacampos').value.trim()=="")document.getElementById('acotacampos').value='"';
    datos = new FormData();
    datos.append("csv", obj.files[0]);
    datos.append("delimitador",document.getElementById('delimitador').value);
    datos.append("acotacampos",document.getElementById('acotacampos').value);
    datos.append("curso_actual",curso_mat);
    datos.append("curso_premat",curso_premat);
    //background: white url('recursos/espera.gif') no-repeat center center
    document.getElementById("progreso").style.display = 'flex';
    lecturaProg=setInterval(actualizaProgreso,1500);
    $.ajax({
        url: "php/secret_csv_matdelphos.php",
        type: 'POST',
        data: datos,
        contentType: false,
        processData: false,
        cache: false
    })
    .done(function(resp) {
        clearInterval(lecturaProg);
        //document.getElementById("progreso_php").innerHTML="";
        document.getElementById("progreso").style.display = 'none';
        if (resp.indexOf("excel/")>-1) window.location.assign("php/"+resp);
        else if (resp == "archivo") alerta("Ha habido un error al subir el archivo.", "Error carga");
        else if (resp == "almacenar") alerta("Ha habido un error al copiar el archivo.", "Error copia");
        else if (resp == "abrir") alerta("El fichero se ha subido pero no puede ser abierto.", "Error FOPEN");
        else if (resp == "noexiste") alerta("El fichero no existe.", "Error archivo");
        else if (resp == "server") alerta("Error de conexión con la base de datos. No se pueden procesar los registros del fichero.", "Error base de datos");
        else if (resp=="informe") alerta("No se puede crear el informe de resultados.", "ERROR CREACIÓN FICHERO");
        else alerta("Fallo de formato porque faltan las siguientes columnas de datos en el fichero csv:<br>"+resp,"ERROR FORMATO. FALTAN COLUMNAS DE DATOS");
        obj.value = null;
    });
}

function actualizaProgreso(){
    $.post("php/progreso_bar.php",{},(r)=>{
        //document.getElementById("progreso_php").innerHTML="Procesando: "+r.procesado+" de "+r.total;
        perc=Math.floor(r.procesado*100/r.total);
        document.getElementById("bar_prog").style.width=perc+"%";
        document.getElementById("bar_prog").innerHTML=r.procesado+"/"+r.total;
    },"json");
}

function descargaCSVnuevosOtraCom(){
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_nuevosotracomunidad.php",
            datos:{
                curso_csv_nuevosotracomunidad:document.getElementById("curso").value
            }
        }
    );
}

function descargaCSVAlNuevos(){
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_nuevos_eso_bach.php",
            datos:{
                curso_csv_nuevos_eso_bach:document.getElementById("curso").value
            }
        }
    );
}

function descargaCSVProgLing(){
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_programaling.php",
            datos:{
                curso_csv_prog_ling:document.getElementById("curso").value
            }
        }
    );
}

function descargaCSVconsolPremat(){
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_consolidaprematricula.php",
            datos:{
                curso_csv_consolidaprematricula:document.getElementById("curso").value
            }
        }
    );
}

function verCertificado(id){
    mostrarPantallaEspera();
    $.post("php/secret_existe_certificado.php",{id_nie:id, curso:document.getElementById("curso").value},(r)=>{
        ocultarPantallaEspera();
        if (r=="ok") window.open("docs/"+id+"/certificado_notas/"+document.getElementById("curso").value+"/"+id+".pdf","_blank");
        else alerta("El alumno no tiene certificado de notas para el curso escolar seleccionado.", "NO EXISTE EL DOCUMENTO");
    });
    
}


function subirMatDelphos(){
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "formulario_subir_mat_delphos","SUBIDA CSV MATRÍCULA DELPHOS",600,2000,"center center","center center")
    .then ((dialogo)=>{
            ocultarPantallaEspera();
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}


function adjuntaResolucion(_id_nie,registro,doc_res){
    //Para convalidaciones
    datos = new FormData();
    datos.append("id_nie",encodeURIComponent(_id_nie));
    datos.append("registro",encodeURIComponent(registro));
    datos.append("resolucion",doc_res.files[0]);
    datos.append("curso",encodeURIComponent(document.getElementById("curso").value));
    mostrarPantallaEspera();
    $.post({
        url:"php/secret_convalid_suberes.php" ,
        data: datos,
        contentType: false,
        processData: false,
        success: function(resp) {
            ocultarPantallaEspera();
            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
            else if (resp == "database") alerta("Hay un problema en la base de datos. Inténtelo más tarde.", "ERROR DB");
            else if (resp == "error_subida") alerta("No se ha podido subir correctamente la resolución. Debe intentarlo en otro momento o revisar el formato del documento.", "ERROR SUBIDA");
            else if (resp == "ok"){
                verRegAdjuntosConvalid(registro);
                alerta("Resolución adjuntada correctamente.","SUBIDA CORRECTA");
            } 
        },
        error: function(xhr, status, error) {
            ocultarPantallaEspera();
            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
        }
    });
}

function cambiaEstadoResolucionConvalidaciones(_rr,obj){
    mostrarPantallaEspera();
    $.post("php/secret_convalid_estado_resol.php",{registro:_rr,estado:obj.value},(resp)=>{
        ocultarPantallaEspera();
        if(resp=="server") alerta("Estado convalidación no cambiado. Hay un problema en el servidor.","ERROR SERVIDOR");
        else if(resp=="config_centro") alerta("No se han podido recuperar datos del centro.","ERROR DB");
        else if(resp=="no_registro")alerta("Estado convalidación no cambiado. No se ha encontrado el registro.","ERROR DB");
        else if(resp=="ok") alerta("El estado de la convalidación se ha cambiado a RESOLUCIÓN "+obj.value,"ESTADO RESOLUCIÓN CAMBIADA");
    })
}



function tipoDocAdjuntoConvalid(obj){
    if (obj.value!='' && obj.value!='Otro') {
        document.getElementById('desc_adic_conval').readOnly=true;
        document.getElementById('desc_adic_conval').value=obj.value;
    }
    else if (obj.value=="Otro"){
        document.getElementById('desc_adic_conval').readOnly=false;
        document.getElementById('desc_adic_conval').value="";
    }
    else if(obj.value==''){
        document.getElementById('desc_adic_conval').value="";
        document.getElementById('desc_adic_conval').readOnly=true;
    } 
}


function adjuntaDocAdicional(_id_nie,registro){
    //Para convalidaciones
    __ministerio=0;
    __consejeria=0;
    var dialogo=generaDivDialog();
    mostrarPantallaEspera();
    $.post("php/secret_convalid_ver_procesado_organismo.php",{registro:registro},(resp)=>{
        ocultarPantallaEspera();
        if(resp.error=='ok'){
            __ministerio=resp.ministerio;
            __consejeria=resp.consejeria;
            c="<div class='row'>";
            c+="<div class='col-1'><label for='tipo_doc_conval' class='col-form-label'>Tipo: </label></div>";
            c+="<div class='col-3'><select class='form-control' id='tipo_doc_conval' name='tipo_doc_conval' size='1' onchange='tipoDocAdjuntoConvalid(this);'/>";
            c+="<option value=''>Selecciona uno...</option>";
            if(resp.ministerio==1)c+="<option value='Resolución del Ministerio'>Resolución del Ministerio</option>";
            if(resp.consejeria==1)c+="<option value='Resolución de Consejería'>Resolución de Consejería</option>";
            c+="<option value='Otro'>Otro</option>";
            c+="</select></div>";
            c+="<div class='col-2'><label for='desc_adic_conval' class='col-form-label'>Descripción: </label></div>";
            c+="<div class='col-5'><input type='text' class='form-control' id='desc_adic_conval' name='desc_adic_conval' maxlength='40' readonly/></div></div>";
            c+="<div class='row' style='margin-top:10px'><div class='col-2'><label for='doc_adic_conval' class='col-form-label'>Documento: </label></div>";
            c+="<div class='col-6'><input type='text' class='form-control' id='doc_adic_conval' readonly placeholder='Seleccionar documento' onclick='document.getElementById(\"conval_doc_adicional\").click()'/></div>";
            c+="</div>";
            c+="<input type='file' id='conval_doc_adicional' name='conval_doc_adicional' multiple='false' accept='application/pdf' style='position:absolute;left:-9999px' onchange='document.getElementById(\"doc_adic_conval\").value=this.files[0].name'/>";
            document.getElementById(dialogo).innerHTML=c;   
            
            $("#"+dialogo).dialog({
                autoOpen: true,
                dialogClass: "no-close",
                modal: true,
                draggable: false,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "ADJUNTAR DOCUMENTO ADICIONAL A CONVALIDACIÓN",
                width: 1000,
                position: { my: "center", at: "center", of: window },
                buttons: [
                    {
                        class: "btn btn-success textoboton",
                        text: "Subir Documento",
                        click: function() {
                            if (document.getElementById("desc_adic_conval").value.trim().length==0 || document.getElementById("doc_adic_conval").value.trim().length==0){
                                alerta("No has seleccionado documento o falta su descripción.", "FALTAN DATOS");
                            }
                            else{
                                datos = new FormData();
                                datos.append("id_nie",encodeURIComponent(_id_nie));
                                datos.append("registro",encodeURIComponent(registro));
                                datos.append("descripcion",encodeURIComponent(document.getElementById("desc_adic_conval").value));
                                datos.append("documento",document.getElementById("conval_doc_adicional").files[0]);
                                datos.append("curso",encodeURIComponent(document.getElementById("curso").value));
                                mostrarPantallaEspera();
                                $.post({
                                    url:"php/secret_convalid_subedocadic.php" ,
                                    data: datos,
                                    contentType: false,
                                    processData: false,
                                    success: function(resp) {
                                        //ocultarPantallaEspera();
                                        if (resp == "servidor"){
                                            ocultarPantallaEspera();
                                            alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                        } 
                                        else if (resp == "database") {
                                            ocultarPantallaEspera();
                                            alerta("Hay un problema en la base de datos. Inténtelo más tarde.", "ERROR DB");
                                        }
                                        else if (resp == "error_subida") {
                                            ocultarPantallaEspera();
                                            alerta("No se ha podido subir correctamente el documento. Debe intentarlo en otro momento o revisar el formato del archivo.", "ERROR SUBIDA");
                                        }
                                        else if (resp == "ok"){
                                            if(document.getElementById("tipo_doc_conval").value=='Resolución del Ministerio' || document.getElementById("tipo_doc_conval").value=='Resolución de Consejería'){
                                                if(document.getElementById("tipo_doc_conval").value=='Resolución del Ministerio'){
                                                    organismo="ministerio";
                                                }
                                                else if(document.getElementById("tipo_doc_conval").value=='Resolución de Consejería'){
                                                    organismo="consejeria";
                                                }
                                                $.post("php/secret_convalid_procesado_organismo.php",{registro:registro,organismo:organismo,estado_procesado:1},(resp)=>{
                                                    ocultarPantallaEspera();
                                                    if(resp=="ok"){
                                                        alerta("Estado procesado cambiado correctamente y resolución adjuntada.", "OK");
                                                    }
                                                    else if(resp=="no_registro") {
                                                        alerta("No existe el registro","ERROR");
                                                    }
                                                    else {
                                                        alerta("No se ha podido cambiar el estado del proceso por algún error interno o de la base de datos.", "ERROR");
                                                        //obj.checked=!obj.checked;
                                                    }
                                                    listaRegistros();
                                                });
                                            }
                                            else{
                                                ocultarPantallaEspera();
                                                alerta("Documento adjuntado correctamente.","SUBIDA CORRECTA");
                                            } 
                                            verRegAdjuntosConvalid(registro);
                                        }
                                
                                        $("#"+dialogo).dialog("destroy").remove();
                                    },
                                    error: function(xhr, status, error) {
                                        ocultarPantallaEspera();
                                        alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                                        $("#"+dialogo).dialog("destroy").remove();
                                    }
                                });
                            }
                            
                        }
                    },
                    {
                    class: "btn btn-success textoboton",
                    text: "Cancelar",
                    click: function() {
                        $("#"+dialogo).dialog("destroy").remove();
                    }
                    }]
            });
        }
        else if(resp.error=="server"){
            alerta("Error en el servidor. Inténtelo más tarde","ERROR SERVIDOR");
        }
        else if(resp.error=="no_registrado"){
            alerta("Registro no encontrado.","NO REGISTRO");
        }
        else{
            alerta("Ha ocurrido algún error. Inténtelo más tarde","ERROR NO DEFINIDO");
        }
    },"json");
}


function adjuntaDocAdicionalExencFCT(_id_nie,registro){
    var dialogo=generaDivDialog();
    c="<div class='row'>";
    c+="<div class='col-5'><label for='desc_adic_exenc_fct' class='col-form-label'>Descripción: </label></div>";
    c+="<div class='col-7'><label for='doc_adic_exenc_fct' class='col-form-label'>Documento: </label></div>";
    c+="</div>";
    c+="<div class='row'>";
    c+="<div class='col-5'><input type='text' class='form-control' id='desc_adic_exenc_fct' name='desc_adic_conval' maxlength='40'/></div>";
    c+="<div class='col-7'><input type='text' class='form-control' id='doc_adic_exenc_fct' readonly placeholder='Seleccionar documento' onclick='document.getElementById(\"exenc_fct_doc_adicional\").click()'/></div>";
    c+="</div>";
    c+="<input type='file' id='exenc_fct_doc_adicional' name='exenc_fct_doc_adicional' multiple='false' accept='application/pdf' style='position:absolute;left:-9999px' onchange='document.getElementById(\"doc_adic_exenc_fct\").value=this.files[0].name'/>";
    document.getElementById(dialogo).innerHTML=c;   
    
    $("#"+dialogo).dialog({
        autoOpen: true,
        dialogClass: "no-close",
        modal: true,
        draggable: false,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "ADJUNTAR DOCUMENTO ADICIONAL A EXENCIÓN DE PFE",
        width: 1000,
        position: { my: "center", at: "center", of: window },
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Subir Documento",
                click: function() {
                    if (document.getElementById("desc_adic_exenc_fct").value.trim().length==0 || document.getElementById("doc_adic_exenc_fct").value.trim().length==0){
                        alerta("No has seleccionado documento o falta su descripción.", "FALTAN DATOS");
                    }
                    else{
                        datos = new FormData();
                        datos.append("id_nie",encodeURIComponent(_id_nie));
                        datos.append("registro",encodeURIComponent(registro));
                        datos.append("descripcion",encodeURIComponent(document.getElementById("desc_adic_exenc_fct").value));
                        datos.append("documento",document.getElementById("exenc_fct_doc_adicional").files[0]);
                        datos.append("curso",encodeURIComponent(document.getElementById("curso").value));
                        mostrarPantallaEspera();
                        $.post({
                            url:"php/secret_exencion_fct_subedocadic.php" ,
                            data: datos,
                            contentType: false,
                            processData: false,
                            success: function(resp) {
                                ocultarPantallaEspera();
                                if (resp == "servidor"){
                                    alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                } 
                                else if (resp == "database") {
                                    alerta("Hay un problema en la base de datos. Inténtelo más tarde.", "ERROR DB");
                                }
                                else if (resp == "error_subida") {
                                    alerta("No se ha podido subir correctamente el documento. Debe intentarlo en otro momento o revisar el formato del archivo.", "ERROR SUBIDA");
                                }
                                else if (resp == "ok"){
                                    alerta("Documento adjuntado correctamente.","SUBIDA CORRECTA");
                                    verRegAdjuntosExencFCT(registro);
                                }
                                
                               
                                $("#"+dialogo).dialog("destroy").remove();
                            },
                            error: function(xhr, status, error) {
                                ocultarPantallaEspera();
                                alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                                $("#"+dialogo).dialog("destroy").remove();
                            }
                        });
                    }
                    
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $("#"+dialogo).dialog("destroy").remove();
            }
            }]
    });
}

function descargaCSVelearningFctProy(){
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_elearning_fctproyecto.php",
            datos:{
                curso_csv_elearning_fctproyecto:document.getElementById("curso").value
            }
        });
}

function parametrosCentro(){
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "formulario_datos_centro","EDICIÓN DATOS ASOCIADOS AL CENTRO",700,2000,"center center","center center",
         [
            {
                class: "btn btn-success textoboton",
                text: "Guardar Cambios",
                click: function() {
                    if ($("#datos_centro").valid()){
                        mostrarPantallaEspera();
                        $.post({
                            url:"php/secret_actualiza_param_centro.php" ,
                            data: $("#datos_centro").serialize(),
                            success: function(resp) {
                                ocultarPantallaEspera();
                                if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                                else if (resp == "ok"){
                                    alerta("Datos del centro actualizados correctamente.","ACTUALIZACIÓN CORRECTA");
                                }
                                else{
                                    alerta(resp,"ERROR");
                                }
                            },
                            error: function(xhr, status, error) {
                                ocultarPantallaEspera();
                                alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                            }
                        });
                        $(this).dialog("destroy").remove();
                    }
                    
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Cancelar",
            click: function() {
                $(this).dialog("destroy").remove();
            }
        }]
    )
    .then((dialogo)=>{
        $.post("php/secret_recupera_param_centro.php",{},(resp)=>{
            ocultarPantallaEspera();
            if (resp.error=="ok"){
                document.getElementById("director").value=resp.registro.director;
                document.getElementById("centro").value=resp.registro.centro;
                document.getElementById("cp").value=resp.registro.cp_centro;
                document.getElementById("direccion").value=resp.registro.direccion_centro;
                document.getElementById("localidad").value=resp.registro.localidad_centro;
                document.getElementById("provincia").value=resp.registro.provincia_centro;
                document.getElementById("tlf_centro").value=resp.registro.tlf_centro;
                document.getElementById("fax_centro").value=resp.registro.fax_centro;
                document.getElementById("email_centro").value=resp.registro.email_centro;
                document.getElementById("email_jef_res").value=resp.registro.email_jefe_residencia;
                document.getElementById("finza_bonif").value=resp.registro.residencia_fianza_bonificados;
                document.getElementById("finza_nobonif").value=resp.registro.residencia_fianza_no_bonificados;

                $("#datos_centro").validate({
                    rules: {
                        director: {
                            required: true
                        },
                        centro: {
                            required: true
                        },
                        direccion:{
                        required: true
                        },
                        cp: {
                            required: true
                        },
                        localidad: {
                            required:true
                        },
                        provincia: {
                            required:true
                        },
                        tlf_centro: {
                            required:true
                        },
                        email_jef_res: {
                            email:true,
                            required:true
                        },
                        finza_bonif: {
                            required:true
                        },
                        finza_nobonif: {
                            required:true
                        }
                    },
                    messages: {
                        director: {
                            required: "Complete el campo"
                        },
                        centro: {
                            required: "Complete el campo"
                        },
                        direccion: {
                            required: "Complete el campo"
                        },
                        cp: {
                            required: "Complete el campo"
                        },
                        localidad: {
                            required: "Complete el campo"
                        },
                        provincia: {
                            required: "Complete el campo"
                        },
                        tlf_centro: {
                            required: "Complete el campo"
                        },
                        email_jef_res:{
                            email:"Dirección no válida",
                            required: "Complete el campo"
                        },
                        finza_bonif: {
                            required: "Complete el campo"
                        },
                        finza_nobonif: {
                            required: "Complete el campo"
                        }
                    },
                    errorPlacement: function(error, element) {
                        $(element).prev().prev($('.errorTxt')).html(error);
                    }
                });
            }
            else if (resp.datos=="server"){
                alerta("Error en servidor. No se pueden editar los datos asociados al centro","ERROR SERVIDOR");
                $("#"+dialogo).dialog("destroy").remove();
            }
        },"json");  
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });    
}


function logosFirmaSello(){
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "div_carga_logos_sellofirma","CAMBIO DE LOGOS Y SELLO Y FIRMA DEL DIRECTOR",1000,2000,"center center","center center",
        [{
            class: "btn btn-success textoboton",
            text: "Terminar",
            click: function() {
                $(this).dialog("destroy").remove();
            }
        }]
    )
    .then((dialogo)=>{
        ocultarPantallaEspera();
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}

function JefesDepartamento(){
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "div_config_departamentos","DATOS ASOCIADOS A LOS DEPARTAMENTOS",800,2000,"center center","center center",
        [
            {
                class: "btn btn-success textoboton",
                text: "Guardar Cambios",
                click: function() {
                    if ($("#config_departamentos").valid()){
                        mostrarPantallaEspera();
                        $.post({
                            url:"php/secret_actualiza_param_departamentos.php" ,
                            data: $("#config_departamentos").serialize(),
                            success: function(resp) {
                                ocultarPantallaEspera();
                                if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                                else if (resp == "database") alerta("No se actualizó ningún registro. Es posible que el valor no haya cambiado.", "FALLO AL ACTUALIZAR");
                                else if (resp == "ok"){
                                    alerta("Datos del Jefe de Departamento actualizados correctamente.","ACTUALIZACIÓN CORRECTA");
                                }
                                else if(resp=="password_duplicada"){
                                    alerta("La contraseña está asignada a otro Jefe de Departamento. Sólo se ha grabado el email.<br>Si desea asignar o cambiar la contraseña, introduzca una nueva que cumpla los requisitos.","PASSWORD DUPLICADA");
                                }
                                else{
                                    alerta(resp,"ERROR");
                                }
                                
                                document.getElementById("config_dpto").value="";
                                document.getElementById("config_nombre_jd").value="";
                                document.getElementById("config_email_jd").value="";
                                document.getElementById("config_password_jd").value=""; 
                                document.getElementById("config_password_jd").readOnly=true;
                                document.getElementById("config_password_jd").placeholder="Seleccione un departamento";
                                document.getElementById("config_email_jd").readOnly=true;
                                document.getElementById("config_email_jd").placeholder="Seleccione un departamento";
                                document.getElementById("config_nombre_jd").readOnly=true;
                                document.getElementById("config_nombre_jd").placeholder="Seleccione un departamento";
                            },
                            error: function(xhr, status, error) {
                                ocultarPantallaEspera();
                                alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                                document.getElementById("config_dpto").value="";
                                document.getElementById("config_nombre_jd").value="";
                                document.getElementById("config_email_jd").value="";
                                document.getElementById("config_password_jd").value=""; 
                                document.getElementById("config_password_jd").readOnly=true;
                                document.getElementById("config_password_jd").placeholder="Seleccione un departamento";
                                document.getElementById("config_email_jd").readOnly=true;
                                document.getElementById("config_email_jd").placeholder="Seleccione un departamento";
                                document.getElementById("config_nombre_jd").readOnly=true;
                                document.getElementById("config_nombre_jd").placeholder="Seleccione un departamento";
                            }
                        });
                    }
                    
                }
            },
            {
            class: "btn btn-success textoboton",
            text: "Salir",
            click: function() {
                document.getElementById("config_dpto").value="";
                document.getElementById("config_nombre_jd").value="";
                document.getElementById("config_email_jd").value="";
                document.getElementById("config_password_jd").value=""; 
                document.getElementById("config_password_jd").readOnly=true;
                document.getElementById("config_password_jd").placeholder="Seleccione un departamento";
                document.getElementById("config_email_jd").readOnly=true;
                document.getElementById("config_email_jd").placeholder="Seleccione un departamento";
                document.getElementById("config_nombre_jd").readOnly=true;
                document.getElementById("config_nombre_jd").placeholder="Seleccione un departamento";        
                $(this).dialog("destroy").remove();
            }
    }])
    .then((dialogo)=>{
        ocultarPantallaEspera();
        generaSelectsDepartamentos();
        $("#config_departamentos").validate({
            rules: {
                config_dpto: {
                    required: true
                },
                config_nombre_jd: {
                    required: true
                },
                config_email_jd: {
                    required: true,
                    email: true
                },
                config_password_jd: {
                    required:false,
                    minlength: 8,
                    password2:true
                }
            },
            messages: {
                config_dpto: {
                    required: "Seleccione un departamento"
                },
                config_nombre_jd: {
                    required: "Complete el campo"
                },
                config_email_jd: {
                    required: "Complete el campo",
                    email: "Formato de email incorrecto"
                },
                config_password_jd:{
                    minlength: "Longitud mínima es de 8 caracteres",
                    password2: "No cumple los requisitos."
                }
            },
            errorPlacement: function(error, element) {
                $(element).prev($('.errorTxt')).html(error);
            }
        });
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}

function selDptoConfigDpto(obj){
    if (obj.value==""){
        document.getElementById("config_nombre_jd").value="";
        document.getElementById("config_nombre_jd").readOnly=true;
        document.getElementById("config_nombre_jd").placeholder="Seleccione un departamento";
        document.getElementById("config_email_jd").value="";
        document.getElementById("config_email_jd").readOnly=true;
        document.getElementById("config_email_jd").placeholder="Seleccione un departamento";
        document.getElementById("config_password_jd").value="";
        document.getElementById("config_password_jd").readOnly=true;
        document.getElementById("config_password_jd").placeholder="Seleccione un departamento";
    }
    else {
        mostrarPantallaEspera();
        $.post("php/secret_recupera_departamentos.php",{},(resp)=>{
            ocultarPantallaEspera();
            if(resp.error!="ok") alerta ("No se han podido consultar los datos de los departamentos.","ERROR DB/SERVER");
            else {
                for (i=0; i<resp.registro.length;i++){
                    if (resp.registro[i].departamento==obj.value){
                        if (resp.registro[i].email_jd.length>0) document.getElementById("config_email_jd").value=resp.registro[i].email_jd;
                        else document.getElementById("config_email_jd").placeholder="";
                        document.getElementById("config_email_jd").readOnly=false;
                        if (resp.registro[i].nombre_ap_jd.length>0) document.getElementById("config_nombre_jd").value=resp.registro[i].nombre_ap_jd;
                        else document.getElementById("config_nombre_jd").placeholder="";
                        document.getElementById("config_nombre_jd").readOnly=false;
                        document.getElementById("config_password_jd").placeholder="";
                        document.getElementById("config_password_jd").readOnly=false;
                        break;
                    }
                }
            }
        },"json");
    }
}


function subeLogo(obj, imagen){
    if (obj.files[0].type !== "image/jpeg" && obj.files[0].type !== "image/jpg") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }

    datos = new FormData();
    datos.append("archivo", obj.files[0]);
    datos.append("tipo",imagen);
    mostrarPantallaEspera();
    $.ajax({
            url: "php/secret_logo_firma.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            ocultarPantallaEspera();
            if (resp == "archivo") {
                alerta("Ha habido un error al subir el archivo.", "Error carga");
                obj.value = null;
            } else if (resp == "almacenar") {
                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                obj.value = null;
            } else if (resp == "ok") {
                var timestamp = new Date().getTime();
                if (imagen=="logo_centro"){
                    document.getElementById("imagen_logo_centro").src = 'recursos/escudo.jpg?t=' + timestamp;
                }
                else if(imagen=="logo_junta"){
                    document.getElementById("imagen_logo_junta").src = 'recursos/logo_ccm.jpg?t=' + timestamp;
                }
                else if(imagen=="firma_sello"){
                    document.getElementById("imagen_firma_sello").src = 'recursos/sello_firma.jpg?t=' + timestamp;
                }
                
                alerta("Imagen actualizada.", "OK");
            }
        });
}


function listadoAutorUsoImag(){
    enviarFormularioSubmit(
        {
            url:"php/secret_csv_autor_uso_imagenes.php",
            datos:{
                curso_csv_autor_uso_imagenes:curso_actual
            }
        });
}


function avisarJefesDpto(){
    var emails=[];
    var departamentos_email=[];
    var desp=document.getElementById('departamento');
    if (desp.value=='Todos'){
        for (i=0;i<desp.options.length;i++){
            if (desp.options[i].value!="Todos"){
                emails.push(desp.options[i].dataset.email);
                departamentos_email.push(desp.options[i].value);
            } 
        }
    }
    else{
        emails.push(desp.options[desp.selectedIndex].dataset.email);
        departamentos_email.push(desp.options[desp.selectedIndex].value);
    } 
    mostrarPantallaEspera();
    $.post("php/secret_exencion_fct_email_jd.php",{emails:emails,departamentos:departamentos_email},(resp)=>{
        ocultarPantallaEspera();
        if (resp=='ok'){
            alerta("Se ha realizado correctamente el aviso a los Jefes de Departamanto.","ENVÍO COMUNICACIÓN OK");
        }
        else if(resp=="server"){
            alerta("Hay un problema en la base de datos o el servidor. Inténtelo en otro momento.","ERROR DB/SERVIDOR");
        }
        else{
            alerta("Ha fallado el envío del aviso a los siguientes departamentos:<br>"+resp,"ERROR/FALLO", false,700);
        }
    });
    
}


function resolucionExencionFCT(registro,dialogo){
confirmar("Se va a generar la resolución.", "RESOLUCIÓN")
.then(function(confirmacion) {
    if (confirmacion) {
        mostrarPantallaEspera();
        $.post("php/secret_exencion_fct_resolucion.php",{registro:registro},(resp)=>{
            ocultarPantallaEspera();
            if (resp=="ok"){
                alerta("La resolución ha sido generada con éxito.","RESOLUCIÓN OK");
                listaRegistros();
                verRegAdjuntosExencFCT(registro);
            }
            else if (resp=="server"){
                alerta("Error en el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
            }
            else if (resp=="no_registro"){
                alerta("No existe el registro","NO REGISTRO");
            }
            else if (resp=="config_centro"){
                alerta("No se ha podido obtener el nombre del director para la firma.","ERROR DB");
            }
            else{
                alerta(resp,"ERROR DB/SERVIDOR");
            }
            $("#"+dialogo).dialog("destroy").remove();
            
        });
    }
    else {
        alerta("Acción cancelada.","CANCELADO");
    }
});
}


function invalidaInformeJDExencionFCT(registro){
    confirmar("¿Está seguro de que desea invalidar el informe del Jefe de Departamento?<br>Si acepta se relaizarán lsa siguientes acciones:<ul><li>Eliminará el informe del Jefe de Departamento</li><li>Eliminará la resolución (si la hubiera)</li><li>Pondrá el registro en estado NO PROCESADO</li>", "INFORME JD")
    .then(function(confirmacion) {
        if (confirmacion) {
            mostrarPantallaEspera();
            $.post("php/secret_exencion_fct_invalida_informe_jd.php",{registro:registro},(resp)=>{
                ocultarPantallaEspera();
                if (resp=="ok"){
                    alerta("Informe de Jefe de Departamento invalidado correctamente.","INFORME INVALIDADO");
                    verRegAdjuntosExencFCT(registro);
                }
                else if (resp=="server"){
                    alerta("Error en el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
                }
                else if (resp=="no_registro"){
                    alerta("No existe el registro","NO REGISTRO");
                }
                /*else if (resp=="no_borrado"){
                    alerta("La solicitud se ha marcado como NO PROCESADA, pero no se ha podido eliminar el fichero del informe del Jefe de Departamento.","FICHERO INFORME NO BORRADO");
                }
                else if (resp=="res_no_borrado"){
                    alerta("El fichero de Resolución que había generado no se ha podido borrar.","FICHERO RESOLUCIÓN NO BORRADO");
                }
                else if( resp=="no_existe"){
                    alerta("No se ha encontrado fichero del informe del Jefe de Departamento que borrar. El registro se ha marcado como NO PROCESADO.","FICHERO NO ENCONTRADO");
                }*/
                else{
                    alerta("Error al invalidar el informe del Jefe de Departamento. Inténtelo más tarde.","ERROR DB/SERVIDOR");
                }
                listaRegistros();
            });
        } else {
            alerta("Acción cancelada.","CANCELADO");
        }
    });
}


function eliminaPrematriculas(){
    if (mes>=5  &&  mes<=12){
        alerta("No se pueden eliminar las prematrículas en el mes actual.<br>Sólo se pueden eliminar desde enero a abril.","INHABILITADO POR FECHA");
        return;
    }
    confirmar("¿Está seguro de que desea eliminar las prematriculas de los alumnos?<br>Si acepta se eliminarán todos los registros de prematrícula de las base de datos y los formularios generados en PDF.<br>Esta acción no se puede deshacer y no se podrán recuperar los datos eliminados.","ELIMINAR PREMATRICULAS",500)
    .then(function(confirmacion1) {
        if (confirmacion1){
            confirmar("Por favor, confirme otra vez que desea eliminar todas las prematríclas.<br>¡¡¡RECUERDE QUE ESTE PROCEDIMIENTO ES IRREVERSIBLE!!!","CONFIRMAR ELIMINACIÓN",400)
            .then(function(confirmacion2) {
                if(confirmacion2){
                    mostrarPantallaEspera();
                    $.post("php/secret_elimina_prematriculas.php",{},(resp)=>{
                        ocultarPantallaEspera();
                        if (resp=="ok"){
                            alerta("Prematrículas eliminadas correctamente.","ELIMINACIÓN CORRECTA");
                        }
                        else if (resp=="server"){
                            alerta("Error en el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
                        }
                        else if (resp=="no_registro"){
                            alerta("No existen prematrículas.","NO HAY PREMATRÍCULAS");
                        }
                        else if (resp=="error_premat_bach" || resp=="error_premat_eso"){
                            if (resp=="error_premat_bach") alerta("No se han podido eliminar las prematrículas de BACHILLERATO.","ERROR DB/SERVIDOR");
                            else if (resp=="error_premat_eso") alerta("No se han podido eliminar las prematrículas de ESO.","ERROR DB/SERVIDOR");
                            else alerta("No se han podido eliminar las prematrículas de BACHILLERATO y ESO.","ERROR DB/SERVIDOR");
                        }
                        else{
                            alerta(resp,"ERROR DB/SERVIDOR");
                        }
                    });
                }
            });
        }
    });
}



function gestionDptos(){
    mostrarPantallaEspera();
    cargaHTML("html/secretaria.htm", "div_departamentos","GESTIÓN DE DEPARTAMENTOS DE FP",800,2000,"center center","center center",
        [
            {
                class: "btn btn-success textoboton btn-sm",
                text: "Nuevo",
                click: function() {
                    document.getElementById("dpto_nombre").value="";
                    document.getElementById("dpto_abreviatura").value="";
                    document.getElementById("dpto_nombre").readOnly=false;
                    document.getElementById("dpto_abreviatura").readOnly=false;
                    div_boton_guardar_cambios.style.visibility="visible";
                    //Inhabilita los botones del dialog
                    $(this).parent().find(".ui-dialog-buttonpane button").prop("disabled", true);
                    document.getElementById("div_desc_operacion").style.visibility='visible';
                    document.getElementById("desc_operacion").innerHTML="ALTA DE NUEVO DEPARTAMENTO";
                    document.getElementById("btn_nuevo_dpto").innerHTML="Añadir";
                }
            },
            {
                class: "btn btn-success textoboton btn-sm",
                text: "Modificar",
                click: function() {
                    div_boton_guardar_cambios.style.visibility="visible";
                    document.getElementById("dpto_nombre").readOnly=false;
                    document.getElementById("dpto_abreviatura").readOnly=false;
                    //Inhabilita los botones del dialog
                    $(this).parent().find(".ui-dialog-buttonpane button").prop("disabled", true);
                    document.getElementById("div_desc_operacion").style.visibility='visible';
                    document.getElementById("desc_operacion").innerHTML="MODIFICACIÓN DE DEPARTAMENTO";
                    document.getElementById("btn_nuevo_dpto").innerHTML="Guardar";
                    document.getElementById("backup_dpto_nombre").value=document.getElementById("dpto_nombre").value;
                    document.getElementById("backup_dpto_abreviatura").value=document.getElementById("dpto_abreviatura").value;
                }
            },
            {
                class: "btn btn-success textoboton btn-sm",
                text: "Borrar",
                click: function() {
                    confirmar("¿Está seguro de que desea eliminar el departamento seleccionado?.","ELIMINAR DEPARTAMENTO")
                    .then(function(confirmacion) {
                        if(confirmacion){
                            confirmar("Por favor, confirme otra vez que desea eliminar el departamento seleccionado.","CONFIRMAR ELIMINACIÓN")
                            .then(function(confirmacion2) {
                                if(confirmacion2){
                                    mostrarPantallaEspera();;
                                    $.post("php/secret_elimina_departamento.php",{dpto:document.getElementById("dpto_select").value},(resp)=>{
                                        ocultarPantallaEspera();
                                        if (resp=="ok"){
                                            alerta("Departamento eliminado correctamente.","ELIMINACIÓN CORRECTA");
                                            mostrarPantallaEspera();;
                                            $.post("php/secret_recupera_departamentos.php",{},(resp)=>{
                                                ocultarPantallaEspera();
                                                if(resp.error!="ok") alerta ("No se han podido regenerar los selectores de los departamentos.","ERROR DB/SERVER");
                                                else {
                                                    departamentos=[];
                                                    for(i=0; i<resp.registro.length;i++){
                                                        departamentos.push(new Array(resp.registro[i].departamento,resp.registro[i].abreviatura,resp.registro[i].email_jd,resp.registro[i].id));
                                                    }
                                                    generaSelectsDepartamentos();
                                                    document.getElementById("dpto_select").selectedIndex=0;
                                                    document.getElementById("dpto_nombre").value=departamentos[0][0];
                                                    document.getElementById("dpto_abreviatura").value=departamentos[0][1];
                                                }
                                            },"json");
                                        }
                                        else if (resp=="server"){
                                            alerta("Error en el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
                                        }
                                        else{
                                            alerta(resp,"ERROR DB/SERVIDOR");
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            },
            {
                class: "btn btn-success textoboton btn-sm",
                text: "Salir",
                click: function() {
                    $(this).dialog("destroy").remove();
                }
            }
        ]
    ).then((dialogo)=>{
        ocultarPantallaEspera();
        generaSelectsDepartamentos();
        document.getElementById("dpto_select").selectedIndex=0;
        gestionSeleccionDpto();
    }).catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });
}

function gestionSeleccionDpto(){
    obj=document.getElementById("dpto_select");
    document.getElementById("dpto_nombre").value=obj.value;
    document.getElementById("dpto_abreviatura").value=obj.options[obj.selectedIndex].dataset.abreviatura;
    document.getElementById("dpto_nombre").readOnly=true;
    document.getElementById("dpto_abreviatura").readOnly=true;
}

function cancelaOPeracionDepartamentos(obj){
    gestionSeleccionDpto();
    obj.parentNode.parentNode.style.visibility='hidden';
    $(obj).closest('.ui-dialog').find('.ui-dialog-buttonpane button').prop('disabled', false);
    document.getElementById("div_desc_operacion").style.visibility='hidden';
    document.getElementById("dpto_nombre").previousElementSibling.innerHTML="";
    document.getElementById("dpto_abreviatura").previousElementSibling.innerHTML="";
}

function guardaAnadeDpto(obj){
    texttextBoton=obj.innerHTML;
    if (document.getElementById("dpto_nombre").value.trim().length==0 || document.getElementById("dpto_abreviatura").value.trim().length==0){
            alerta("Los campos 'Nombre del Departamento' y 'Abreviatura' son obligatorios.","FALTAN CAMPOS OBLIGATORIOS");
            return;
    }
    if (document.getElementById("dpto_nombre").parentNode.innerHTML=="Nombre duplicado" || document.getElementById("dpto_abreviatura").parentNode.innerHTML=="Duplicada"){
        alerta("Los campos 'Nombre del Departamento' y 'Abreviatura' no pueden estar duplicados.","NOMBRE O ABREVIATURA DUPLICADOS");
        return;
    }
    if (textBoton=="Añadir"){
        mostrarPantallaEspera();;
        $.post("php/secret_anade_departamento.php",{dpto_nombre:document.getElementById("dpto_nombre").value,dpto_abreviatura:document.getElementById("dpto_abreviatura").value},(resp)=>{
            ocultarPantallaEspera();
            if (resp=="ok"){
                alerta("Departamento añadido correctamente.","ALTA CORRECTA");
                mostrarPantallaEspera();;
                $.post("php/secret_recupera_departamentos.php",{},(resp)=>{
                    ocultarPantallaEspera();
                    if(resp.error!="ok") alerta ("No se han podido regenerar los selectores de los departamentos.","ERROR DB/SERVER");
                    else {
                        departamentos=[];
                        for(i=0; i<resp.registro.length;i++){
                            departamentos.push(new Array(resp.registro[i].departamento,resp.registro[i].abreviatura,resp.registro[i].email_jd,resp.registro[i].id));
                        }
                        generaSelectsDepartamentos();
                        document.getElementById("dpto_select").selectedIndex=0;
                        document.getElementById("dpto_nombre").value=departamentos[0][0];
                        document.getElementById("dpto_abreviatura").value=departamentos[0][1];
                    }
                },"json");
            }
            else if (resp=="server"){
                alerta("Error en el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
            }
            else{
                alerta(resp,"ERROR DB/SERVIDOR");
            }
        });
    }
    else if(textBoton=="Guardar"){
        if (document.getElementById("dpto_nombre").value==document.getElementById("backup_dpto_nombre").value && document.getElementById("dpto_abreviatura").value==document.getElementById("backup_dpto_abreviatura").value){
            alerta("No se han realizado cambios en el departamento. No se realizará ninguna acción.","SIN CAMBIOS");
        }
        else{
            nom_dpto=document.getElementById("dpto_nombre").value;
            abr_dpto=document.getElementById("dpto_abreviatura").value;
            id_dpto=document.getElementById("dpto_select").options[document.getElementById("dpto_select").selectedIndex].dataset.id;
            $.post("php/secret_modifica_departamento.php",{dpto_nombre:nom_dpto,dpto_abreviatura:abr_dpto, dpto_id:id_dpto},(resp)=>{  
                if (resp=="ok"){
                    alerta("Departamento modificado correctamente.","MODIFICACIÓN CORRECTA");
                    mostrarPantallaEspera();;
                    $.post("php/secret_recupera_departamentos.php",{},(resp)=>{
                        ocultarPantallaEspera();
                        if(resp.error!="ok") alerta ("No se han podido regenerar los selectores de los departamentos.","ERROR DB/SERVER");
                        else {
                            departamentos=[];
                            for(i=0; i<resp.registro.length;i++){
                                departamentos.push(new Array(resp.registro[i].departamento,resp.registro[i].abreviatura,resp.registro[i].email_jd,resp.registro[i].id));
                            }
                            generaSelectsDepartamentos();
                        }
                    },"json");
                }
                else if (resp=="server"){
                    alerta("Error en el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
                }
                else{
                    alerta(resp,"ERROR DB/SERVIDOR");
                }
            }); 
        }
    }
    div_boton_guardar_cambios.style.visibility="hidden";
    document.getElementById("dpto_nombre").readOnly=true;
    document.getElementById("dpto_abreviatura").readOnly=true;
    $(obj).closest('.ui-dialog').find('.ui-dialog-buttonpane button').prop('disabled', false);
    document.getElementById("div_desc_operacion").style.visibility='hidden';
}

function compruebaDuplicadoDpto(obj){
    var valor = obj.value;
    var id = obj.id;
    id_dpto=document.getElementById("dpto_select").options[document.getElementById("dpto_select").selectedIndex].dataset.id;
    var accion="";
    
    if(document.getElementById("btn_nuevo_dpto").innerHTML=="Añadir"){
        accion="alta";
    }
    else if(document.getElementById("btn_nuevo_dpto").innerHTML=="Guardar"){
        accion="modifica";  
    }
    if (id=="dpto_nombre"){
        //mostrarPantallaEspera();;
        $.post("php/secret_comprueba_duplicado_dpto.php",{valor:valor, tipo_input:"nombre",accion:accion,id:id_dpto},(resp)=>{
            //ocultarPantallaEspera();
            if (resp=="duplicado" || resp=="duplicado_normalizado"){
                obj.previousElementSibling.innerHTML="Nombre duplicado";
            }
            else{
                obj.previousElementSibling.innerHTML="";
            }
        }); 
    }
    else if (id=="dpto_abreviatura"){
        //mostrarPantallaEspera();;
        $.post("php/secret_comprueba_duplicado_dpto.php",{valor:valor, tipo_input:"abreviatura",accion:accion,id:id_dpto},(resp)=>{
            //ocultarPantallaEspera();
            if (resp=="duplicado" || resp=="duplicado_normalizado"){
                obj.previousElementSibling.innerHTML="Duplicada";
            }
            else{
                obj.previousElementSibling.innerHTML="";
            }   
        });
    }
}

