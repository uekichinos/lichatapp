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

                        <div class="bg-gray-200 bg-opacity-25 grid grid-flow-col grid-cols-3 grid-rows-1">
                            <div class="p-6 col-span-2" v-if="rooms.data">
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div v-for="(room, index) in rooms.data" :key="index" class="border-1 rounded-md bg-white shadow">
                                        <div class="grid grid-cols-1 md:grid-cols-1 rounded-t-md p-5 text-md font-bold bg-indigo-500">
                                            <span class="text-white font-semibold">
                                                {{ room.name }} <br>
                                                <span class="font-normal text-sm italic">
                                                    by {{ room.person }}
                                                    <span v-if="room.private == true">(Private)</span>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="pl-5 pt-5 pr-5 text-sm text-gray-500">{{ room.desc }}</div>
                                        <div class="p-5 grid grid-cols-1 gap-4">
                                            <div class="text-sm text-gray-400">
                                                <jet-button @click="enterRoom(room.id_encrypt)" class="bg-green-300 hover:bg-green-500 mr-3">
                                                    chat
                                                </jet-button>
                                                <jet-button v-if="room.isowner == true" @click="deleteRoom(room.id_encrypt)" class="bg-red-300 hover:bg-red-500">
                                                    terminate
                                                </jet-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-6">
                                    <pagination :links="rooms.links" />
                                </div>
                            </div>
                            <div class="p-6 border-gray-200 border-l border-gray-200">
                                <form @submit.prevent="submit">
                                    <div class="text-2xl font-bold">Create Room</div>
                                    <input type="text" v-model="form.name" class="w-full mb-3 rounded-md border-gray-300 text-sm" placeholder="room name"><br>
                                    <textarea v-model="form.desc" class="w-full mb-1 rounded-md border-gray-300 text-sm" rows="4" placeholder="description"></textarea>
                                    <div class="block">
                                        <div>
                                            <label class="inline-flex items-center">
                                                <Checkbox v-model="form.private" class="form-checkbox text-indigo-600" />
                                                <span class="ml-2">Private</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="grid grid-flow-col grid-cols-1 grid-rows-2 mt-5">
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
import Checkbox from "@/Jetstream/Checkbox";

export default {
    components: {
        JetButton,
        JetNavLink,
        AppLayout,
        Pagination,
        Checkbox,
    },
    props: {
        rooms: Object,
        errors: Object,
    },
    data() {
        return {
            form: { name: "", desc: "", private: false }, 
        };
    },
    methods: {
        submit() {
            this.$inertia.post(route("room.store", this.form));
        },
        deleteRoom(index) {
            if(confirm("Do you really want to delete?")){
                this.$inertia.delete(route("room.destroy", index));
            }
        },
        enterRoom(index) {
            this.$inertia.get(route("message.index", index));
        }
    },
};
</script>
