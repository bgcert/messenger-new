<template>
    <div class="messenger">
    	<div class="threads">
    		<div>
    			<h3>Messenger {{ owner.name }}</h3>
    			<!-- Search -->
    			<div class="ui fluid icon input loading">
    				<input type="text" name="search" placeholder="Search..." v-model="searchText" @focus="focus = true" @blur="focus = false">
    				<i class="search icon"></i>
    			</div>

    			<!-- Search searchResults -->
    			<div v-if="searchResults.length > 0">
    				<div class="ui middle aligned selection list">
    					<h4>Search results</h4>
	    				<div class="item" v-for="result in searchResults" @click.prevent="selectUser(result)">
	    					<img class="ui avatar image" src="https://placeimg.com/200/200/2">
	    					<div class="content">
	    						<div class="header" >{{ result.name }}</div>
	    					</div>
	    				</div>
	    			</div>
    			</div>
    			<hr>
    		</div>

    		<!-- Thread list -->
    		<div v-if="activeThread.id && !focus">
    			<h4>Contacts</h4>
    			<div class="ui middle aligned selection list" >
    				<div class="item" v-for="thread in threads" :class="{ selected: (!isNewThread && activeThread.id == thread.id) }" @click.prevent="setThread(thread.id)">
    					<img class="ui avatar image" src="https://placeimg.com/200/200/2">
    					<div class="content">
    						<div class="header" >{{ thread.first_participant.user.name }}</div>
    					</div>
    				</div>
    			</div>
    		</div>
    		<div v-else>
    			loading ...
    		</div>
    	</div>

    	<div class="ui feed">
    		<!-- Feed participant -->
    		<div class="header" v-if="activeParticipant.id">
    			<h4> Participant: {{ activeParticipant.name }}</h4>
    		</div>

    		<template v-if="messages.length > 0">
    			<div class="event conversation" v-for="message in messages">
	    			<div class="label">
	    				<template v-if="message.user_id == activeThread.first_participant.user_id">
	    					<img src="https://placeimg.com/200/200/2">
	    				</template>
	    				<template v-else>
	    					<img src="https://placeimg.com/200/200/25">
	    				</template>
	    			</div>
	    			<div class="content">
	    				<p>
	    					{{ message.body }}
	    					<span v-if="!message.local && activeThread.first_participant.read_message_id == message.id">
	    						(seen)
	    					</span>
	    				</p>
	    			</div>
	    		</div>
    		</template>

    		<!-- Message input -->
    		<div class="message-box">
    			<div class="ui form">
	    			<div class="field">
	    				<textarea
	    					rows="2"
	    					name="message"
	    					v-model="messageText"
	    					@keyup.enter="send"
	    					@focus="seen"
	    					placeholder="Write a message ...">
	    				</textarea>
	    			</div>
	    		</div>
    		</div>
    		
    	</div>

    	<div class="details">
    		
    	</div>
    </div>
</template>

<script>
    export default {
    	props: ['owner'],

    	data: function () {
    		return {
    			loading: false,
    			threads: {},
    			messages: [],
    			activeThread: {},
    			activeParticipant: {},
    			searchText: '',
    			searchResults: [],
    			messageText: '',
    			isNewThread: false,
    			lastMessageIsRead: false,
    			focus: false
    		}
    	},

    	methods: {
    		allThreadsByCurrentUser() {
    			return new Promise( function(resolve, reject) {
	    			axios.get('/api/threads')
		    			.then(function (response) {
						    resolve(response.data);
						})
						.catch(function (error) {
						    console.log(error);
						    reject('user threads not loaded');
						})
				});
    		},

    		setThread(id) {
    			let vm = this;
    			axios.get('/api/thread/' + id)
	    			.then(function (response) {
	    				vm.activeThread = response.data;
	    				vm.activeParticipant = response.data.first_participant.user;
					})
					.catch(function (error) {
					    console.log(error);
					})
    		},

    		getMessages(id) {
				let vm = this;
    			axios.get('/api/thread/' + id + '/messages')
	    			.then(function (response) {
	    				vm.messages = response.data;
					})
					.catch(function (error) {
					    console.log(error);
					})
    		},

    		send() {
    			let vm = this;
    			let route;
    			// Add message
    			if (!this.isNewThread) {
	    			route = '/api/thread/' + this.activeThread.id + '/message/add';
	    			axios.post(route, {
	    				body: vm.messageText
	    			})
	    			.then(function (response) {
	    				console.log(response.data);
	    				vm.messages.push(response.data);
	    				vm.messageText = '';
	    			})
	    			.catch(function (error) {
	    				console.log(error);
	    			});
	    			return;
	    		}

    			
				// New message
    			route = '/api/thread/message/new';
    			axios.post(route, {
    				user_id: vm.activeParticipant.id,
    				body: vm.messageText
    			})
    			.then(function (response) {
    				vm.activeThread.id = response.data.id; // Setting the id before unshifting the thread for faster class 'selected' responce
    				vm.threads.unshift(response.data);
    				vm.setThread(response.data.id);
    				vm.messageText = '';
    				vm.isNewThread = false;
    			})
    			.catch(function (error) {
    				console.log(error);
    			});

    			
    		},

    		searchUsers() {
    			console.log('searching for users ...');
    			let vm = this;
    			axios.post('/api/search', {
    				text: vm.searchText
    			})
    			.then(function (response) {
    				vm.searchResults = response.data
    			})
    			.catch(function (error) {
    				console.log(error);
    			});
    		},

    		selectUser(user) {
    			// Check if thread with this user exist
    			let res = this.threads.find( thread => thread.first_participant.user.id === user.id);
    			if (res) {
    				this.setThread(res.id);
    			} else {
    				this.isNewThread = true;
    				this.searchText = '';
    				this.activeParticipant = user;
    				this.messages = [];
    			}
    			this.searchResults = [];
    			// console.log(user);
    		},

    		filteredContacts() {

    		},

    		seen() {
    			if (this.isNewThread) {
    				return;
    			}
    			let vm = this;
    			if (!this.lastMessageIsRead) {
    				let route = '/api/thread/seen';
	    			axios.post(route, {
	    					thread_id: vm.activeThread.id,
	    					read_message_id: vm.messages[vm.messages.length - 1].id // last message id
	    				})
		    			.then(function (response) {
		    				vm.lastMessageIsRead = true;
						    console.log(response.data)
						})
						.catch(function (error) {
						    console.log(error);
						})
    			}
    		},

    		async load() {
    			this.threads = await this.allThreadsByCurrentUser();

    			// This will run after previous is fully loaded!
    			if (this.threads.length > 0) {
    				this.setThread(this.threads[0].id);
    			}
    		}
    	},

    	watch: {
		    activeThread: function (val) {
		    	this.getMessages(val.id);
		    },

		    searchText: function (val) {
		    	if (val.length > 2) {
		    		this.searchUsers();
		    	}
		    }
		},

        mounted() {
            console.log('Messenger mounted.');
            this.load();
        }
    };
</script>

<style lang="scss">

	.messenger {
		display: flex;
		height: calc(100vh - 5px);

		.threads {
			padding: 20px;
			border-right: 1px solid grey;
			flex: 1;

			.selected {
				background-color: rebeccapurple !important;
				font-weight: bold;
				.content .header {
					color: white;
				}
			}
		}

		.feed {
			flex: 2;
			background-color: white;
			display: flex;
   			flex-direction: column;
   			justify-content: center;

   			.header {
   				border-bottom: 1px solid grey;
   				padding: 15px;
   			}

   			.conversation {
   				flex: 1 1 0%;
   				padding: 15px;
   			}

			.message-box {
				padding: 15px;
				border-top: 1px solid grey;
			}
		}

		.details {
			flex: 1;
			background-color: green;
		}
	}
</style>