<template>
    <div class="w-[600px] h-[80px] flex items-center">
        <TextInput 
            type="text"
            class="block w-full mr-2"
            v-model="search"
            autocomplete
            @keyup.enter.prevent="onSearch"
            placeholder="Search for files and folders"
        />
    </div>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import TextInput from '../TextInput.vue';
    import {router} from '@inertiajs/vue3';
    import { emitter, ON_SEARCH } from '@/event-bus';

    const search = ref('');
    let params = '';

    function onSearch(){
        params.set('search', search.value);
        router.get(window.location.pathname + '?' + params.toString());
        
        emitter.emit(ON_SEARCH, search.value);
    }

    onMounted(() => {
        params = new URLSearchParams(window.location.search)
        search.value = params.get('search')
    })
</script>