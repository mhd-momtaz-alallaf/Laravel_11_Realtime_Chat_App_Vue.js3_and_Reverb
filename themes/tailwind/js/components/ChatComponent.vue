<template>
    <div>
        <!-- messages aria -->
        <div class="flex flex-col justify-end h-80">
            <div class="p-4 overflow-y-auto max-h-fit">
                <div v-for="message in messages" :key="message.id" class="flex items-center mb-2">

                    <!-- showing blue background if the current user is the sender of the message -->
                    <div v-if="message.sender_id == currentUser.id" class="p-2 ml-auto text-white bg-blue-500 rounded-lg">
                        {{ message.text }}
                    </div>

                    <!-- showing gray background otherwise (if the current user is not the sender of the message) -->
                    <div v-else class="p-2 mr-auto bg-gray-200 rounded-lg">
                        {{ message.text }}
                    </div>
                </div>
            </div>
        </div>

        <!-- the text messages input and send button -->
        <div class="flex items-center">
            <input
                type="text"
                v-model="newMessage"
                @keydown="sendTypingEvent"
                @keyup.enter="sendMessage"
                placeholder="Type a message..."
                class="flex-1 px-2 py-1 border rounded-lg"
            />

            <button @click="sendMessage" class="px-4 py-1 ml-2 text-white bg-blue-500 rounded-lg">
                Send
            </button>
        </div>
    </div>
</template>

<script setup>
    import axios from 'axios';
    import { onMounted, ref } from 'vue';

    // Receiving the friend and the curring-user that passed to the component.
    const props = defineProps({
        friend: {
            type: Object,
            required: true,
        },
        currentUser: {
            type: Object,
            required: true,
        },
    });

    const messages= ref([]);

    const newMessage= ref("");

    const sendMessage = () => {
        if (newMessage.value.trim() !== "") {
            messages.value.push({
                id: messages.value.length + 1,

                text: newMessage.value,

                isMe: true,
            });

            newMessage.value = "";
        }
    };

    // getting the messages from the 'messages' api route.
    onMounted( () => {
        axios.get(`/messages/${props.friend.id}`)
        .then( (response) => {
            console.log(response.data);
            messages.value = response.data;
        });
    });
</script>
