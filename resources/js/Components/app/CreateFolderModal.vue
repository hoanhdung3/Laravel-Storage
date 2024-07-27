<template>
    <Modal :show="modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Create New Folder
            </h2>
            <div class="mt-6">
                <InputLabel for="folderName" value="Folder Name" class="sr-only" />
                <TextInput
                    type="text"
                    ref="folderNameInput"
                    id="folderName"
                    v-model="form.name"
                    class="mt-1 block w-full"
                    :class="form.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    placeholder="Folder name"
                    @keyup.enter="createFolder"
                />
                <InputError :message="form.errors.name" />
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>
                <PrimaryButton 
                    class="ml-3" 
                    @click="createFolder" 
                    :disable="form.processing" 
                    :class="{'opacity-25' : form.processing}"
                >
                    Submit
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
    import Modal from "@/Components/Modal.vue";
    import InputLabel from "../InputLabel.vue";
    import TextInput from "../TextInput.vue";
    import InputError from "../InputError.vue";
    import SecondaryButton from "../SecondaryButton.vue";
    import {useForm, usePage} from "@inertiajs/vue3";
    import PrimaryButton from "../PrimaryButton.vue";
    import {nextTick, ref} from "vue";

    const form = useForm({
        name: '',
        parent_id: null
    });

    const page = usePage();

    const folderNameInput = ref(null);

    const emit = defineEmits(['update:modelValue']);

    const {modelValue} = defineProps({
        modelValue: Boolean
    });

    function onShow(){
        nextTick(() => folderNameInput.value.focus)
    }

    function createFolder(){
        form.parent_id = page.props.folder.id;
        form.post(route('folder.create'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                form.reset();
            },
            onError: () => {
                folderNameInput.value.focus()
            }
        });
    }

    function closeModal(){
        emit('update:modelValue');
        form.clearErrors();
        form.reset();
    }
</script>