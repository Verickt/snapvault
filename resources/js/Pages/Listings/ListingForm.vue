<template>
    <div>
        <!-- Step 1: Image Upload -->
        <form v-if="step === 1" @submit.prevent="submitImages">
            <JetLabel for="images" value="Upload Images" />
            <input
                id="images"
                type="file"
                multiple
                accept="image/*"
                @change="handleImageUpload"
            />
            <JetInputError :message="form.errors.images" />

            <!-- Image Previews -->
            <div v-if="imagePreviewUrls.length">
                <p>Image Previews:</p>
                <div v-for="(url, index) in imagePreviewUrls" :key="index" class="image-preview">
                    <img :src="url" alt="Image Preview" />
                </div>
            </div>

            <JetButton type="submit" class="mt-4">Upload Images</JetButton>
        </form>

        <!-- Step 2: Edit Details -->
        <form v-if="step === 2" @submit.prevent="submitDetails">
            <!-- Display Uploaded Images -->
            <div v-if="form.image_paths.length" class="uploaded-images mt-4">
                <p>Uploaded Images:</p>
                <div v-for="(path, index) in form.image_paths" :key="index" class="uploaded-image">
                    <img :src="getFullPath(path)" alt="Uploaded Image" />
                </div>
            </div>

            <JetLabel for="title" value="Title" class="mt-4" />
            <JetInput
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                autofocus
            />
            <JetInputError :message="form.errors.title" />
            <JetButton @click.prevent="generateTitle">Generate Title</JetButton>

            <JetLabel for="description" value="Description" class="mt-4" />
            <JetTextarea
                id="description"
                v-model="form.description"
                class="mt-1 block w-full"
            />
            <JetInputError :message="form.errors.description" />
            <JetButton @click.prevent="generateDescription">Generate Description</JetButton>

            <JetButton type="submit" class="mt-4">Save Details</JetButton>
        </form>

        <!-- Loading Spinner for Suggestions -->
        <div v-if="isSuggestionsLoading" class="loading-spinner">
            Processing...
        </div>

        <!-- Success Message -->
        <div v-if="message" class="mt-4">
            <p>{{ message }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import JetButton from '@/Components/PrimaryButton.vue';
import JetInputError from '@/Components/InputError.vue';
import JetLabel from '@/Components/InputLabel.vue';
import JetInput from '@/Components/TextInput.vue';
import JetTextarea from '@/Components/TextInput.vue';
import axios from 'axios';

const props = defineProps({
    listing: {
        type: Object,
        default: () => ({
            id: null,
            title: '',
            description: '',
            image_paths: [],
        }),
    },
    message: {
        type: String,
        default: '',
    },
});

const page = usePage();
const isEditMode = computed(() => !!props.listing.id);
const step = ref(isEditMode.value ? 2 : 1);
const imagePreviewUrls = ref([]);
const isSuggestionsLoading = ref(false);

const form = useForm({
    id: props.listing.id,
    title: props.listing.title || '',
    description: props.listing.description || '',
    images: null,
    image_paths: props.listing.image_paths || [],
});

onMounted(() => {
    if (page.props.message) {
        step.value = 2;
    }
});

const getFullPath = (path) => {
    return path.startsWith('http') ? path : `/storage/${path}`;
};

const handleImageUpload = (event) => {
    form.images = event.target.files;
    imagePreviewUrls.value = Array.from(event.target.files).map((file) =>
        URL.createObjectURL(file)
    );
};

const submitImages = () => {
    form.post(route('listings.store'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            step.value = 2;
        },
    });
};

// Generate title using all images
const generateTitle = async () => {
    isSuggestionsLoading.value = true;
    try {
        const response = await axios.post(route('listings.generateTitle', { id: form.id }), {
            images: form.image_paths
        });
        form.title = response.data.title;
    } catch (error) {
        console.error('Error generating title:', error);
    } finally {
        isSuggestionsLoading.value = false;
    }
};

// Generate description using all images
const generateDescription = async () => {
    isSuggestionsLoading.value = true;
    try {
        const response = await axios.post(route('listings.generateDescription', { id: form.id }), {
            images: form.image_paths
        });
        form.description = response.data.description;
    } catch (error) {
        console.error('Error generating description:', error);
    } finally {
        isSuggestionsLoading.value = false;
    }
};

const submitDetails = () => {
    form.put(route('listings.update', form.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Handle success (e.g., show a success message, redirect, etc.)
        },
    });
};
</script>

<style scoped>
.image-preview img,
.uploaded-image img {
    max-width: 100px;
    margin: 5px;
}

.loading-spinner {
    /* Add your loading spinner styles here */
}
</style>
