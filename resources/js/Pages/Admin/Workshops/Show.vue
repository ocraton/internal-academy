<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    workshop: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Dettaglio Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dettaglio Workshop
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-8 shadow sm:rounded-lg dark:bg-gray-800">
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Titolo</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ workshop.title }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrizione</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ workshop.description }}</dd>
                        </div>
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
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ workshop.capacity }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creato il</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ new Date(workshop.created_at).toLocaleString('it-IT') }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6 flex items-center gap-4">
                        <Link
                            :href="route('admin.workshops.edit', workshop.id)"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Modifica
                        </Link>
                        <Link
                            :href="route('admin.workshops.index')"
                            class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
                        >
                            Torna alla lista
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
