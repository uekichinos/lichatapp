<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                            <div class="mt-8 text-2xl">{{ room[0].name }}</div>

                            <div class="mt-6 text-gray-500">
                                {{ room[0].desc }}
                            </div>
                        </div>

                        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                            <div class="p-6" v-if="messages.data">
                                <div v-for="(message, index) in messages.data" :key="index" class="mb-4 pb-4 border-b-2">
                                    <div class="text-gray-400 font-bold text-sm">
                                        {{ message.person }}
                                        <span @click="deleteMessage(message.id_encrypt)" class="cursor-pointer float-right font-normal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#db272d">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                    <span v-html="message.text"></span><br>
                                    <div class="text-gray-400 italic text-right text-sm">
                                        {{ message.cleandate }}
                                    </div>
                                    <div></div>
                                </div>
                                <pagination :links="messages.links" />
                            </div>

                            <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                                <form @submit.prevent="submit">
                                    <input type="hidden" class="w-full" v-model="form.id_encrypt">
                                    <textarea v-model="form.text" class="w-full" rows="4"></textarea>

                                    <div class="grid grid-flow-col grid-cols-4 grid-rows-1">
                                        <div class="w-20">
                                            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                                Send
                                            </jet-button>
                                        </div>
                                        <div class="w-auto col-span-3 text-right text-red-400">
                                            <div v-if="errors.text">{{ errors.text }}</div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>


<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import Pagination from "@/Jetstream/Pagination";

export default {
    components: {
        JetButton,
        AppLayout,
        Pagination,
    },
    props: {
        room: Array,
        id_encrypt: String,
        errors: Object,
        messages: Object,
    },
    data() {
        return {
            form: { text: "", id_encrypt: this.id_encrypt },
        };
    },
    methods: {
        submit() {
            this.$inertia.post("/messages", this.form);
        },
        deleteMessage(id) {
            if(confirm("Do you really want to delete?")){
                this.$inertia.delete("/messages/"+id);
            }
        }
    },
};
</script>
