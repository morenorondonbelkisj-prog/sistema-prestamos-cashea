<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Préstamos - BNPL</title>
    <!-- Tailwind CSS CDN para Estilizado Rápido -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div id="app" class="p-6 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <header class="mb-8 flex justify-between items-center bg-white p-6 rounded-xl shadow">
            <div>
                <h1 class="text-2xl font-bold text-blue-900">Plataforma de Préstamos Cashea</h1>
                <p class="text-sm text-gray-500">Módulo Integrado: Backend REST, DB Políglota y OLAP</p>
            </div>
            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                Sistema 100% Operativo
            </span>
        </header>

        <!-- Dashboard / Métricas Rápidas (OLAP) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-xl shadow border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase font-bold">Total Préstamos</p>
                <p class="text-2xl font-extrabold text-gray-800">[[ resumenOlap.total_prestamos || 0 ]]</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase font-bold">Monto Financiado (USD)</p>
                <p class="text-2xl font-extrabold text-green-600">$[[ resumenOlap.monto_total_financiado || '0.00' ]]</p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase font-bold">Cartera en Mora</p>
                <p class="text-2xl font-extrabold text-yellow-600">[[ resumenOlap.mora_activa || 0 ]] casos</p>
            </div>
        </div>

        <!-- Tabla de Préstamos en Vivo -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold mb-4 text-gray-700">Listado de Préstamos Activos</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-xs text-gray-500 uppercase">
                        <th class="p-3">ID</th>
                        <th class="p-3">Cliente</th>
                        <th class="p-3">Total (USD)</th>
                        <th class="p-3">Financiado (USD)</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Cuotas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in prestamos" :key="p.id" class="border-b hover:bg-gray-50 text-sm">
                        <td class="p-3 font-semibold">#[[ p.id ]]</td>
                        <td class="p-3">[[ p.cliente?.persona?.nombre ]] [[ p.cliente?.persona?.apellido ]]</td>
                        <td class="p-3">$[[ p.monto_total_usd ]]</td>
                        <td class="p-3 font-bold text-blue-600">$[[ p.monto_financiado_usd ]]</td>
                        <td class="p-3">
                            <span :class="{
                                'bg-green-100 text-green-800': p.estado === 'ACTIVO',
                                'bg-red-100 text-red-800': p.estado === 'EN_MORA',
                                'bg-gray-100 text-gray-800': p.estado === 'PAGADO'
                            }" class="px-2 py-1 text-xs font-bold rounded">
                                [[ p.estado ]]
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">
                                [[ p.cuotas?.length || 0 ]] cuotas
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        createApp({
            delimiters: ['[[', ']]'],
            data() {
                return {
                    prestamos: [],
                    resumenOlap: {}
                }
            },
            mounted() {
                this.cargarDatos();
            },
            methods: {
                cargarDatos() {
                    axios.get('/api/v1/prestamos').then(res => {
                        this.prestamos = res.data;
                    });
                    axios.get('/api/v1/analytics/cartera-resumen').then(res => {
                        this.resumenOlap = res.data;
                    });
                }
            }
        }).mount('#app');
    </script>
</body>
</html>