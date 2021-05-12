<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rooms
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                            <div class="mt-8 text-2xl">Chat Rooms</div>

                            <div class="mt-6 text-gray-500">
                                Page description here. Bla bla bla lorem ipsum lorem ipsum
                                lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum
                                lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum
                                lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum
                            </div>
                        </div>

                        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-4">
                            <div class="p-6 col-span-3" v-if="rooms.data">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div v-for="(room, index) in rooms.data" :key="index" class="p-5 border-1 rounded-md bg-white shadow">
                                        <div class="text-md font-bold">
                                            {{ room.name }} <span class="font-normal text-sm italic">by {{ room.person }}</span>

                                            <span @click="deleteRoom(room.id_encrypt)" class="cursor-pointer float-right font-normal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#db272d">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="mt-3 text-sm text-gray-500">{{ room.desc }}</div>
                                        <div class="mt-3 text-sm text-gray-400 float-right">
                                            <jet-button @click="enterRoom(room.id_encrypt)" class="bg-green-300 hover:bg-green-500 mr-5">
                                                chat
                                            </jet-button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-6">
                                    <pagination :links="rooms.links" />
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                                <form @submit.prevent="submit">
                                    <div class="text font-bold">Create Room</div><br>
                                    <input type="text" v-model="form.name" class="w-full mb-3 rounded-md border-gray-300 text-sm" placeholder="room name"><br>
                                    <textarea v-model="form.desc" class="w-full mb-3 rounded-md border-gray-300 text-sm" rows="4" placeholder="description"></textarea>

                                    <div class="grid grid-flow-col grid-cols-1 grid-rows-2">
                                        <div class="w-full">
                                            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                                Create
                                            </jet-button>
                                        </div>
                                        <div v-if="errors" class="w-full text-left text-red-400">
                                            <div v-for="(key, index) in errors" :key="index">
                                                {{ key }}<br>
                                            </div>
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
import JetNavLink from '@/Jetstream/NavLink'
import Pagination from "@/Jetstream/Pagination";

export default {
    components: {
        JetButton,
        JetNavLink,
        AppLayout,
        Pagination,
    },
    props: {
        rooms: Object,
        errors: Object,
    },
    data() {
        return {
            form: { name: "", desc: "" },
        };
    },
    methods: {
        submit() {
            this.$inertia.post("/rooms", this.form);
        },
        deleteRoom(index) {
            if(confirm("Do you really want to delete?")){
                this.$inertia.delete("/rooms/"+index);
            }
        },
        enterRoom(index) {
            this.$inertia.get("/chat/"+index);
        }
    },
};
</script>
