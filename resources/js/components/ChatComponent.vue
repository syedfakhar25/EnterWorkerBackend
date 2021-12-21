
<template>
   <div class="row">
    <div class="col-5">
            <div class="card card-default">
                <div class="card-header"  >Active Users</div>
                <div class="card-body">
                    <ul style="list-style: none;">
                        <li class="py-2" v-for="(user1, index) in users" :key="index" v-if="user1.id != user.id" >
                           <span v-on:click="changeChat(user1);">{{ user1.first_name }} {{ user1.last_name }}</span> <samp><small>({{ user1.designation }})</small></samp> 
                           <p class="text-muted" v-if="activeUser.id==user1.id" >  is typing...</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
       <div class="col-7">
           <div class="card card-default" v-if="chatWithUser">
               <div class="card-header">{{chatWithUser.first_name}} {{chatWithUser.last_name}} 
                <!-- <span class="text-muted" v-if="activeUser" >{{ activeUser.first_name }} {{ activeUser.last_name }}  is typing...</span> -->
                <span class="text-muted" v-if="activeUser" >  is typing...</span>
               </div>
               <div class="card-body p-0">
                   <ul class="list-unstyled" style="height:300px; overflow-y:scroll" v-chat-scroll>
                       <li class="p-2" v-for="(message, index) in messages" :key="index" v-bind:class = "message.user.id==user.id ?'text-right':'text-left'">
                           <!-- <strong>{{ message.user.first_name }} {{ message.user.last_name }}</strong> -->
                           {{ message.message }}
                       </li>
                   </ul>
               </div>

               <input
                    @keydown="sendTypingEvent"
                    @keyup.enter="sendMessage"
                    v-model="newMessage"
                    type="text"
                    name="message"
                    placeholder="Enter your message..."
                    class="form-control">
           </div>
       </div>

   </div>
</template>

<script>
    export default {
        props:['user'],
        data() {
            return {
                messages: [],
                newMessage: '',
                users:[],
                activeUser: false,
                typingTimer: false,
                chatWithUser:'',
                usersList: [],
            }
        },
        created() {
          axios.get('users-list').then(response => {
                    this.users = response.data;
                })
            this.fetchMessages();

            Echo.join('chat')
                .here(user => {
                //     this.users = user;
                $.each(this.users, function(key, value) {
                  if(value.id==user.id){
                    value.live_status=1;
                  }
                    })
                })
                .joining(user => {
                  // this.users.push(user);
                 console.log(user);
                 console.log(this.users);
                  $.each(this.users, function(key, value) {
                  if(value.id==user.id){
                    value.live_status=1;
                  }
                    })
                           
                  //        });
                  // end testing
                    
                })
                .leaving(user => {
                  $.each(this.users, function(key, value) {
                  if(value.id==user.id){
                    value.live_status=0;
                  }
                    })
                    // this.users = this.users.filter(u => u.id != user.id);
                })
                .listen('ChatEvent',(event) => {
                    this.messages.push(event.chat);
                })
                .listenForWhisper('typing', user => {
                   this.activeUser = user;
                    if(this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }
                   this.typingTimer = setTimeout(() => {
                       this.activeUser = false;
                   }, 1000);
                })
        },
        methods: {
            fetchMessages() {
                axios.post('fetch-messages', {user_id: this.user.id, chatwith_id: this.chatWithUser.id}).then(response => {
                    this.messages = response.data;
                })
            },
            sendMessage() {
                this.messages.push({
                    user: this.user,
                    message: this.newMessage
                });
                axios.post('messages', {message: this.newMessage, chatwith_id: this.chatWithUser.id});
                this.newMessage = '';
            },
            sendTypingEvent() {
                Echo.join('chat')
                    .whisper('typing', this.user);
                console.log(this.user.name + ' is typing now')
            },

            changeChat: function (event) {
              alert(event.id);
              this.chatWithUser=event;
              this.fetchMessages();
                // this.chatWith = user;
            }
        }
    }
</script> 