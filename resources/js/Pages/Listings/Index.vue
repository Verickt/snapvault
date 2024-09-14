<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Listings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="$page.props.flash && $page.props.flash.success" class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div class="mb-4 flex justify-end">
                    <Link
                        :href="route('listings.create')"
                        class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Create New Listing
                    </Link>
                </div>
                <!-- Listings Table -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 overflow-x-scroll">
                    <table class="min-w-full divide-y divide-gray-200 ">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" >
                        <tr v-for="listing in listings.data" :key="listing.id">
                            <td class="px-6 py-4 whitespace-nowrap  max-w-xl">
                                {{ listing.title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ listing.description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="route('listings.show', listing.id)" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                    View
                                </Link>
                                <Link :href="route('listings.edit', listing.id)" class="text-blue-600 hover:text-blue-900 mr-2">
                                    Edit
                                </Link>
                                <button @click="confirmDeletion(listing.id)" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <!-- Pagination (if applicable) -->
                    <!-- Add pagination controls here if you're paginating listings -->
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <jet-dialog-modal :show="confirmingDelete" @close="confirmingDelete = false">
            <template #title>Delete Listing</template>
            <template #content>
                <p>Are you sure you want to delete this listing? This action cannot be undone.</p>
            </template>
            <template #footer>
                <jet-secondary-button @click="confirmingDelete = false">Cancel</jet-secondary-button>
                <jet-danger-button @click="deleteListing" class="ml-2">Delete</jet-danger-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import JetDialogModal from '@/Components/DialogModal.vue';
import JetSecondaryButton from '@/Components/SecondaryButton.vue';
import JetDangerButton from '@/Components/DangerButton.vue';

const { listings} = usePage().props;

const confirmingDelete = ref(false);
const listingToDelete = ref(null);

const confirmDeletion = (id) => {
    confirmingDelete.value = true;
    listingToDelete.value = id;
};

console.log(listings);
const deleteListing = () => {
    router.delete(route('listings.destroy', listingToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingDelete.value = false;

            listings.data = listings.data.filter(
                (listing) => listing.id !== listingToDelete.value
            );

            listingToDelete.value = null;
        },
    });
};

</script>

<style>

</style>
