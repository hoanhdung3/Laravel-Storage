<template>
    <Modal :show="props.modelValue" @show="onShow" max-width="sm">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                Share Files
            </h2>
            <div class="mt-6">
                <InputLabel for="shareEmail" value="Enter Email Address" class="sr-only" />
                <TextInput
                    type="text"
                    ref="emailInput"
                    id="shareEmail"
                    v-model="form.email"
                    class="mt-1 block w-full"
                    placeholder="Enter Email Addresses"
                    :class="form.errors.email ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    @keyup.enter="share"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="closeModal">
                    Cancel
                </SecondaryButton>
                <PrimaryButton 
                    class="ml-3" 
                    @click="share" 
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
    import { showSuccessNotification } from "@/event-bus";

    const form = useForm({
        email: null,
        all: false,
        ids: [],
        parent_id: null
    });

    const page = usePage();

    const emailInput = ref(null);

    const emit = defineEmits(['update:modelValue']);

    const props = defineProps({
        modelValue: Boolean,
        allSelected: Boolean,
        selectedIds: Array
    });

    function onShow(){
        nextTick(() => emailInput.value.focus)
    }

    function share(){
        form.parent_id = page.props.folder.id;
        if(props.allSelected){
            form.all = true;
            form.ids = [];
        }else{
            form.ids = props.selectedIds;
        }
        const email = form.email;
        form.post(route('file.share'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                form.reset();
                showSuccessNotification(`Selected files will be shared to "${email}" if the emails exists in the system`);
            },
            onError: () => {
                emailInput.value.focus()
            }
        });
    }

    function closeModal(){
        emit('update:modelValue');
        form.clearErrors();
        form.reset();
    }
</script>