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

                        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-3">
                            <div class="col-span-2 p-6" v-if="messages.data">
                                <div v-for="(message, index) in messages.data" :key="index" class="mb-4 pb-4 border-b-2">
                                    <div class="text-gray-400 font-bold text-sm">
                                        {{ message.person }}
                                        <span v-if="message.isowner == true" @click="deleteMessage(message.id_encrypt)" class="cursor-pointer float-right font-normal">
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
                                    <div class="text-2xl font-bold">Message</div>
                                    <input type="hidden" class="w-full" v-model="form.id_encrypt">
                                    <textarea v-model="form.text" class="w-full mb-1 rounded-md border-gray-300 text-sm" rows="4" placeholder="type your message"></textarea>
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
                                <br><br>

                                <div class="" v-if="members.data">
                                    <div class="text-2xl font-bold">Members</div>
                                    <div v-for="(member, index) in members.data" :key="index" class="font-bold text-sm mt-3 mb-3">
                                        {{ member.person }}
                                        <span v-if="room_owner == true && member.memberid != room[0].owner" @click="deleteMember(member.id_encrypt)" class="cursor-pointer float-right font-normal">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#db272d">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <br><br>

                                <div v-if="room_owner == true">
                                    <form @submit.prevent="invite">
                                        <div class="text-2xl font-bold">Invitation</div>
                                        <input type="hidden" class="w-full" v-model="forminvite.id_encrypt">
                                        <div class="grid grid-flow-col grid-cols-4 grid-rows-1">
                                            <div class="col-span-3 pr-1">
                                                <input type="text" class="w-full mb-3 rounded-md border-gray-300 text-sm" v-model="forminvite.email" placeholder="send email invitation">
                                            </div>
                                            <div class="text-right">
                                                <jet-button :class="{ 'opacity-25': forminvite.processing }" :disabled="forminvite.processing">
                                                    Invite
                                                </jet-button>                                            
                                            </div>
                                        </div>
                                        <div class="w-auto col-span-3 text-left text-red-400">
                                            <div v-if="errors.email">{{ errors.email }}</div>
                                        </div>
                                    </form>                                    
                                    <div class="mb-4 pb-4" v-if="invites.data">
                                        <div v-for="(invite, index) in invites.data" :key="index" class="font-bold text-sm mt-3 mb-3">
                                            {{ invite.email }}
                                            <span @click="deleteInvite(invite.id_encrypt)" class="cursor-pointer float-right font-normal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#db272d">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <br><br>
                                </div>

                                <div v-if="room_owner == false" @click="deleteMember(my_id)">
                                    <button class="w-full bg-red-500 hover:bg-red-400 text-white font-bold py-2 px-4 border-b-4 border-red-700 hover:border-red-500 rounded">
                                        Leave Room
                                    </button>
                                </div>
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
        invites: Object,
        members: Object,
        room_owner: Boolean,
        my_id: String,
    },
    data() {
        return {
            form: { text: "", id_encrypt: this.id_encrypt },
            forminvite: { email: "", id_encrypt: this.id_encrypt },
        };
    },
    methods: {
        submit() {
            this.$inertia.post(route("message.store", this.form));
        },
        invite() {
            this.$inertia.post(route("member.invite", this.forminvite));
        },
        deleteMessage(id) {
            if(confirm("Do you really want to delete?")){
                this.$inertia.delete(route("message.destroy", id));
            }
        },
        deleteInvite(id) {
            if(confirm("Do you really want to delete?")){
                this.$inertia.delete(route("member.invitedestroy", id));
            }
        },
        deleteMember(id) {
            if(confirm("Do you really want to remove member from this room?")){
                this.$inertia.delete(route("member.destroy", id));
            }
        }, 
    },
};
</script>
