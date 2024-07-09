<template>
    <div>
        <!-- messages aria -->
        <div class="flex flex-col justify-end h-80">
            <div ref="messagesContainer" class="p-4 overflow-y-auto max-h-fit">
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

        <!-- The text messages input and send button -->
        <div class="flex items-center">
            <!-- @keydown is the event of start pressing on the keyboard keys (typing) -->
            <!-- @keyup.enter triggers the sendMessage function to send the message when the Enter key is pressed -->
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

        <!-- Friend typing... status -->
        <small v-if="isFriendTyping" class="text-gray-700">
            {{ friend.name }} is typing...
        </small>
    </div>
</template>

<script setup>
import axios from 'axios';
import { defineProps, nextTick, onMounted, ref, watch } from 'vue';

// Receiving the friend and the curring-user that passed to the component and Defines them as required props.
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

// the messages array that contains all the messages to loop on it in the template.
const messages = ref([]);

// the new sent message.
const newMessage = ref("");

// to handel the scrolling of the page a new message is received.
const messagesContainer = ref(null);

// to handel the typing.. status when the users start typing
const isFriendTyping = ref(false);

// to handel the time of showing 'friend is typing...'
const isFriendTypingTimer = ref(null);

// Watch for changes in messages to auto-scroll
watch(messages, async () => {
    // Uses nextTick to ensure the DOM is updated before scrolling.
    await nextTick();
    messagesContainer.value.scrollTo({
        top: messagesContainer.value.scrollHeight,
        behavior: 'smooth',
    });
}, { deep: true });

// Send a new message
const sendMessage = () => {
    if (newMessage.value.trim()) {
        // triggering the back-end api, sending a request to back-end to store the new message into the database.
        axios.post(`/messages/${props.friend.id}`, {
            message: newMessage.value,
        }).then(response => {
            messages.value.push(response.data);
            newMessage.value = '';
        });
    }
};

// Handling sending typing... event
const sendTypingEvent = () => {
    // we will send the typing... status to the friend only, so we passed the friend id to the channel route
    // sends a whisper event on the private chat.{friend.id} channel.
    Echo.private(`chat.${props.friend.id}`).whisper("typing", {
        userID: props.currentUser.id,
    });
};

// Fetch initial messages and set up listeners
onMounted(() => {
    // getting the messages from the 'messages' api route.
    axios.get(`/messages/${props.friend.id}`).then( (response) => {
        messages.value = response.data;
    });

    // receiving the broadcasted message to show it to the receiver in realtime.
    // Laravel Echo is a JavaScript library that makes it easy to work with WebSockets in Laravel applications. It provides a simple API for subscribing to channels and listening for events broadcast by the Laravel backend.
    Echo.private(`chat.${props.currentUser.id}`)
        // listening to the MessageSent Event
        .listen('MessageSent', (response) => {
            // pushing the new message to the messages array that we loop on it in the template.
            messages.value.push(response.message);
        })
        .listenForWhisper("typing", (response) => {
            // isFriendTyping.value is set to true if the user who is typing is the friend. Otherwise, it is set to false.
            isFriendTyping.value = response.userID === props.friend.id;

            // if there is already an existing typing timer (isFriendTypingTimer.value), it is cleared using clearTimeout
            if (isFriendTypingTimer.value) {
                clearTimeout(isFriendTypingTimer.value);
            }

            // A new timer is set with setTimeout, This timer will set isFriendTyping.value to false after 800 milliseconds (0.8 seconds), This means the typing indicator will disappear if no new typing event is received within 800 milliseconds.
            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 800);
        });
});
// Whisper is a feature provided by Laravel Echo that allows you to send small bits of information to other clients connected to the same private channel. These whispers are not stored on the server; they are only sent to other clients subscribed to the same channel, making them useful for real-time interactions like indicating a user is typing.
</script>
