var efct = angular.module("exencFCT", []);
/*
efct.factory("factFormacion", function() {
    var tipoFormSel = [0, 0, 0];
    return { tipoFormSel: tipoFormSel };
});
*/
efct.controller("ctrlFormacion", ["$scope", function($scope) {
    var tipoFormSel = [0, 0, 0];

    $scope.tipoForm = ["-- Seleccione una --",
        "Formación Profesional Básica",
        "Formación Profesional de Grado Medio",
        "Formación Profesional de Grado Superior"
    ];

    $scope.formGM = ["-- Seleccione una --",
        "Cocina y Gastronomía",
        "Gestión Administrativa",
        "Instalaciones Eléctricas y Automáticas",
        "Instalaciones Frigoríficas y de Climatización",
        "Instalaciones de Producción de Calor",
        "Panadería, Repostería y Confitería",
        "Servicios en Restauración"
    ];

    $scope.formGS = ["-- Seleccione una --",
        "Administración y Finanzas",
        "Agencias de Viajes y Gestión de Eventos",
        "Asistencia a la Dirección",
        "Automatización y Robótica Industrial",
        "Dirección de Cocina",
        "Gestión de Alojamientos Turísticos",
        "Guía, Información y Asistencia Turísticas",
        "Mantenimiento de Instalaciones Térmicas y Fluidos",
        "Sistemas Electrotécnicos y Automatizados"
    ];

    $scope.formFPB = ["-- Seleccione una --",
        "Alojamiento y Lavandería",
        "Cocina y Restauración",
        "Electricidad y Electrónica",
        "Servicios Administrativos"
    ];

    $scope.valTipoForm = $scope.tipoForm[0];
    $scope.valFormGM = $scope.formGM[0];
    $scope.valFormGS = $scope.formGS[0];
    $scope.valFormFPB = $scope.formFPB[0];

    $scope.cambiaTipoForm = function() {
        switch ($scope.valTipoForm) {
            case "Formación Profesional Básica":
                tipoFormSel = [1, 0, 0];
                break;
            case "Formación Profesional de Grado Medio":
                tipoFormSel = [0, 1, 0];
                break;
            case "Formación Profesional de Grado Superior":
                tipoFormSel = [0, 0, 1];
                break;
        }

        $scope.muestraGradoMedio = tipoFormSel[1];
        $scope.muestraGradoSuperior = tipoFormSel[2];
        $scope.muestraFPB = tipoFormSel[0];
    }

    $scope.cambiaFormGM = function() {

    }

    $scope.cambiaFormGS = function() {

    }

    $scope.cambiaFormFPB = function() {

    }

}]);