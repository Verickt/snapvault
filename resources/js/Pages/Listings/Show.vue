<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ listing.title }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div
                    v-if="$page.props.flash && $page.props.flash.success"
                    class="mb-4 p-4 bg-green-100 text-green-800 rounded"
                >
                    {{ $page.props.flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Listing Details -->
                    <h3 class="text-2xl font-bold mb-4">{{ listing.title }}</h3>
                    <p class="mb-6">{{ listing.description }}</p>
                    {{listing.image_paths}}

                    <!-- Images -->
                    <div v-if="listing.image_paths && listing.image_paths.length > 0" class="mb-6">
                        <h4 class="text-xl font-semibold mb-2">Images</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div v-for="(path, index) in listing.image_paths" :key="index">
                                <img
                                    :src="`/storage/${path}`"
                                    alt="Listing Image"
                                    class="w-full h-auto object-cover rounded-md"
                                />
                            </div>
                        </div>
                    </div>


                    <!-- Actions -->
                    <div class="mt-6 flex items-center">
                        <Link
                            :href="route('listings.edit', listing.id)"
                            class="text-blue-600 hover:text-blue-900 mr-4"
                        >
                            Edit Listing
                        </Link>
                        <button
                            @click="confirmDeletion(listing.id)"
                            class="text-red-600 hover:text-red-900"
                        >
                            Delete Listing
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <jet-dialog-modal :show="confirmingDelete" @close="confirmingDelete = false">
            <template #title>Delete Listing</template>
            <template #content>
                <p>
                    Are you sure you want to delete this listing? This action cannot be undone.
                </p>
            </template>
            <template #footer>
                <jet-secondary-button @click="confirmingDelete = false">
                    Cancel
                </jet-secondary-button>
                <jet-danger-button @click="deleteListing" class="ml-2">
                    Delete
                </jet-danger-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script setup>
import { ref } from 'vue';
import {  router,Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import JetDialogModal from '@/Components/DialogModal.vue';
import JetSecondaryButton from '@/Components/SecondaryButton.vue';
import JetDangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    listing: {
        type: Object,
        required: true,
    },
});

const listing = ref(props.listing.data);

const confirmingDelete = ref(false);
const listingToDelete = ref(null);

const confirmDeletion = (id) => {
    confirmingDelete.value = true;
    listingToDelete.value = id;
};

const deleteListing = () => {
    router.delete(route('listings.destroy', listingToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingDelete.value = false;
            listingToDelete.value = null;

            // Redirect to the listings index page after deletion
            router.visit(route('listings.index'), {
                preserveState: false,
                replace: true,
            });
        },
    });
};
</script>
