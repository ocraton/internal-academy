<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    workshop: Object,
});

const page = usePage();
const flash = computed(() => page.props.flash);

function enroll(workshop) {
    router.post(route('employee.workshops.enroll', workshop.id), {}, {
        preserveScroll: true,
    });
}

function unenroll(workshop) {
    router.delete(route('employee.workshops.unenroll', workshop.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="workshop.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('employee.workshops.index')"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    ← Torna alla lista
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ workshop.title }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Flash messages -->
                <div v-if="flash?.success" class="mb-6 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ flash.success }}</p>
                </div>
                <div v-if="flash?.error" class="mb-6 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ flash.error }}</p>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <span
                                v-if="workshop.is_full"
                                class="rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 dark:bg-red-900 dark:text-red-200"
                            >
                                Completo
                            </span>
                            <span
                                v-else
                                class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900 dark:text-green-200"
                            >
                                {{ workshop.available_spots }} posti disponibili
                            </span>
                        </div>

                        <dl class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data inizio</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ new Date(workshop.starts_at).toLocaleString('it-IT') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Data fine</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ new Date(workshop.ends_at).toLocaleString('it-IT') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Capienza</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ workshop.capacity }} posti totali</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Iscritti</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ workshop.enrolled_count }}</dd>
                            </div>
                        </dl>

                        <div class="mb-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrizione</dt>
                            <dd class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ workshop.description }}</dd>
                        </div>

                        <div class="flex gap-3">
                            <button
                                v-if="workshop.is_enrolled_by_current_user"
                                type="button"
                                class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                @click="unenroll(workshop)"
                            >
                                Cancella iscrizione
                            </button>
                            <button
                                v-else-if="!workshop.is_full"
                                type="button"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                @click="enroll(workshop)"
                            >
                                Iscriviti
                            </button>
                            <button
                                v-else
                                type="button"
                                disabled
                                class="cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-400"
                            >
                                Completo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
