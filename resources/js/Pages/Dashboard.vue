<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                            <div class="mt-8 text-2xl">Dashboard</div>

                            <div class="mt-6 text-gray-500">
                                Page description here. Bla bla bla lorem ipsum lorem ipsum
                                lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum
                                lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum
                                lorem ipsum lorem ipsum lorem ipsum lorem ipsum lorem ipsum
                            </div>
                        </div>

                        <div class="bg-gray-200 bg-opacity-25">
                            <div class="grid grid-flow-col grid-cols-3 grid-rows-1">
                                <div class="col-span-2 p-6">
                                    Analytics
                                </div>
                                <div class="p-6 border-l border-gray-200">
                                    <div v-if="invites.data != ''">
                                        <div class="text-2xl font-bold">Invitation</div>
                                        <div v-for="(invite, index) in invites.data" :key="index" class="text-sm mt-3 mb-3">
                                            By <strong>{{ invite.person }}</strong> to join <strong>{{ invite.roomname }}</strong>
                                            <span @click="acceptInvite(invite.id_encrypt)" class="cursor-pointer float-right font-normal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#32a852">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                            <span @click="declineInvite(invite.id_encrypt)" class="cursor-pointer float-right font-normal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="#db272d">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
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

export default {
    components: {
        AppLayout,
    },
    props: {
        invites: Object,
    },
    methods: {
        declineInvite(id) {
            if(confirm("Do you really want to decline invitation?")){
                this.$inertia.post(route("member.invitedecline", id));
            }
        },
        acceptInvite(id) {
            if(confirm("Do you really want to accept invitation?")){
                this.$inertia.post(route("member.inviteaccept", id));
            }
        }
    },
};
</script>
