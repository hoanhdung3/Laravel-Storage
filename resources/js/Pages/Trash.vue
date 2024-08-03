<template>
    <AuthenticatedLayout>
        <nav class="flex itemsc-center justify-end p-1 mb-3">
            <div>
                <DeleteForeverButton :all-selected="allSelected" :selected-ids="selectedIds" @delete="resetForm" />
                <RestoreFilesButton :all-selected="allSelected" :selected-ids="selectedIds" @restore="resetForm" />
            </div>
        </nav>
        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left w-[30px] max-w-[30px] pr-0">
                            <Checkbox 
                                v-model:checked="allSelected" 
                                @change="onSelectAllChange"
                            />
                        </th>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                            Name
                        </th>
                        <th class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                            Path                        
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr 
                        v-for="file of allFiles.data"
                        @click="$event => toggleFileSelect(file)"
                        :key="file.id"
                        class="cursor-pointer border-b transition duration-300 ease-in-out hover:bg-blue-100"
                        :class="(selected[file.id] || allSelected) ? 'bg-blue-50' : 'bg-white'"
                    >
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900  max-w-[30px] pr-0">
                            <Checkbox 
                                :checked="selected[file.id] || allSelected" 
                                v-model="selected[file.id]"
                                @change="$event => onSelectCheckboxChange(file)"
                            />
                        </td>    
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center">
                            <FileIcon :file="file" />
                            {{ file.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ file.path }}
                        </td>
                    </tr>
                </tbody>
            </table>
        
            <div v-if="!allFiles.data.length" class="py-8 text-center text-sm text-gray-400">
                There is no data in this folder
            </div>
            <div ref="loadMoreIntersect">

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import {router, Link} from '@inertiajs/vue3';
    import {HomeIcon} from '@heroicons/vue/20/solid'
    import FileIcon from '@/Components/app/FileIcon.vue';
    import { computed, onMounted, onUpdated, ref } from 'vue';
    import { httpGet } from '@/Helper/http-helper';
    import Checkbox from '@/Components/Checkbox.vue';
    import DeleteFilesButton from '@/Components/app/DeleteFilesButton.vue';
    import DownloadFilesButton from '@/Components/app/DownloadFilesButton.vue';
    import RestoreFilesButton from '@/Components/app/RestoreFilesButton.vue';
import DeleteForeverButton from '@/Components/app/DeleteForeverButton.vue';

    const allSelected = ref(false);
    const selected = ref({});
    
    const loadMoreIntersect = ref(null);

    const props = defineProps({
        files: Object,
        folder: Object,
        ancestors: Object
    });

    const selectedIds = computed(() => Object.entries(selected.value).filter(a => a[1]).map(a => a[0]));

    const allFiles = ref({
        data: props.files.data,
        next: props.files.links.next
    });

    function loadMore(){
        if(allFiles.value.next === null){
            return;
        }

        httpGet(allFiles.value.next).then(res => {
            allFiles.value.data = [...allFiles.value.data, ...res.data];
            allFiles.value.next = res.links.next;
        });
    }

    function onSelectAllChange(){
        allFiles.value.data.forEach(f => {
            selected.value[f.id] = allSelected.value;
        });
    }

    function toggleFileSelect(file){
        selected.value[file.id] = !selected.value[file.id];
        onSelectCheckboxChange(file);
    }

    function onSelectCheckboxChange(file){
        if(!selected.value[file.id]){
            allSelected.value = false;
        }else{
            let checked = true;
            for(let file of allFiles.value.data){
                if(!selected.value[file.id]){
                    checked = false;
                    break;
                }
            }
            allSelected.value = checked;
        }

    }

    function resetForm(){
        allSelected.value = false;
        selected.value = {};
    }

    function onDelete(){
        allSelected.value = false;
        selected.value = {};
    }
    
    onUpdated(() => {
        allFiles.value = {
            data: props.files.data,
            next: props.files.links.next
        }
    })

    onMounted(() => {
        const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMore()) ,{
            rootMargin: '-250px 0px 0px 0px'
        });

        observer.observe(loadMoreIntersect.value);
    });

</script>