<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    workshops: Object,
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
    <Head title="Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Workshop
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash messages -->
                <div v-if="flash?.success" class="mb-6 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ flash.success }}</p>
                </div>
                <div v-if="flash?.error" class="mb-6 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                    <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ flash.error }}</p>
                </div>

                <div v-if="workshops.data.length === 0" class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        Nessun workshop disponibile.
                    </div>
                </div>

                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="workshop in workshops.data"
                        :key="workshop.id"
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                    >
                        <div class="p-6">
                            <div class="mb-3 flex items-start justify-between">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                    <Link
                                        :href="route('employee.workshops.show', workshop.id)"
                                        class="hover:text-indigo-600 dark:hover:text-indigo-400"
                                    >
                                        {{ workshop.title }}
                                    </Link>
                                </h3>
                                <span
                                    v-if="workshop.is_full"
                                    class="ml-2 shrink-0 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200"
                                >
                                    Completo
                                </span>
                                <span
                                    v-else
                                    class="ml-2 shrink-0 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200"
                                >
                                    {{ workshop.available_spots }} posti disponibili
                                </span>
                            </div>

                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ new Date(workshop.starts_at).toLocaleString('it-IT') }}
                            </p>

                            <p class="mb-4 line-clamp-3 text-sm text-gray-600 dark:text-gray-300">
                                {{ workshop.description }}
                            </p>

                            <div class="mt-auto">
                                <button
                                    v-if="workshop.is_enrolled_by_current_user"
                                    type="button"
                                    class="w-full rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                    @click="unenroll(workshop)"
                                >
                                    Cancella iscrizione
                                </button>
                                <button
                                    v-else-if="!workshop.is_full"
                                    type="button"
                                    class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    @click="enroll(workshop)"
                                >
                                    Iscriviti
                                </button>
                                <button
                                    v-else
                                    type="button"
                                    disabled
                                    class="w-full cursor-not-allowed rounded-md bg-gray-300 px-4 py-2 text-sm font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-400"
                                >
                                    Completo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
