<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { onMounted, onUnmounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps<{
    most_popular: object | null
    workshop_stats: object[]
    upcoming_count: number
    total_enrollments: number
}>()

let interval

onMounted(() => {
    interval = setInterval(() => {
        router.reload({ only: ['most_popular', 'workshop_stats', 'upcoming_count', 'total_enrollments'] })
    }, 10000)
})

onUnmounted(() => {
    clearInterval(interval)
})

function formatDate(isoDate: string): string {
    return new Date(isoDate).toLocaleDateString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>

<template>
    <Head title="Dashboard Statistiche" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Dashboard Statistiche
                </h2>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span>
                    LIVE
                </span>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">

                <!-- KPI Cards -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">

                    <!-- Workshop più popolare -->
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Workshop più popolare</p>
                        <template v-if="most_popular">
                            <p class="mt-2 truncate text-lg font-bold text-gray-900 dark:text-white">
                                {{ most_popular.title }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ formatDate(most_popular.starts_at) }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                {{ most_popular.enrolled_count }} / {{ most_popular.capacity }} iscritti
                            </p>
                        </template>
                        <p v-else class="mt-2 text-sm text-gray-400 dark:text-gray-500">Nessun dato</p>
                    </div>

                    <!-- Workshop in arrivo -->
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Workshop in arrivo</p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ upcoming_count }}
                        </p>
                    </div>

                    <!-- Iscrizioni totali -->
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Iscrizioni totali</p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ total_enrollments }}
                        </p>
                    </div>

                    <!-- Workshop totali -->
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Workshop totali</p>
                        <p class="mt-2 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ workshop_stats.length }}
                        </p>
                    </div>

                </div>

                <!-- Tabella iscrizioni per workshop -->
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Iscrizioni per Workshop</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Nome Workshop
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Data
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Iscritti
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        In attesa
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Capienza
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                        Riempimento
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                <tr
                                    v-for="stat in workshop_stats"
                                    :key="stat.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ stat.title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ formatDate(stat.starts_at) }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white">
                                        {{ stat.enrolled_count }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white">
                                        {{ stat.waitlisted_count }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white">
                                        {{ stat.capacity }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                <div
                                                    class="bg-blue-600 h-2 rounded-full transition-all"
                                                    :style="{ width: stat.fill_percentage + '%' }"
                                                ></div>
                                            </div>
                                            <span class="w-10 shrink-0 text-right text-sm text-gray-600 dark:text-gray-300">
                                                {{ stat.fill_percentage }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="workshop_stats.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Nessun workshop trovato.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
