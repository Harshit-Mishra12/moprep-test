
let currentDate = null;
let replyMsgId = "";
function formatISTDate() {
    const options = {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZoneName: 'short',
      timeZone: 'UTC',
    };
  
    return options;
  }
// Function to send a message
function sendMessage() {
	
    const fileInput = document.getElementById('file-input');
    const messageInput = document.getElementById('message-input');
    const messageText = messageInput.value.trim();
    const loader = document.getElementById('loader');
    const paperClip = document.getElementById('loader-paperclip');
    const loaderSending = document.getElementById('loader-sending');
    const replyInput = document.getElementById('reply-input');
    const replyText = replyInput.innerText.trim();
    // Get the selected file
    let fileSize = null;
    const file = fileInput.files[0];
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        fileSize = file.size;
      }

    let currentDateTime = new Date();
    currentDateTime.setHours(currentDateTime.getHours() + 5, currentDateTime.getMinutes() + 30);
    let createdAtTimestamp = Math.floor(currentDateTime.getTime() );
    // console.log(createdAtTimestamp);

    if (messageText !== '' || file) {
        let messagetextData = {
            "author": authorData,
            "createdAt": createdAtTimestamp,
            "height": 0,
            "name": authorData.firstName,
            "size": fileSize,
            "id": "",
            "status": "seen",
            "text": messageText,
            "type": "text",
            "uri": "",
            "width": 0.0,
            "filename": file ? file.name : "",
            "replyText": replyText ? replyText : "",
            "replyTextId": replyMsgId ? replyMsgId : "",
            "chat_pinned": "0",
            "adminstatus":"seen"
        };


        if (file) {
            loader.style.display = 'block';
            paperClip.style.display = 'none';
            fileInput.style.display = 'none';
            
            const storageRef = firebase.storage().ref();
            const fileName = `${new Date().getTime()}_${file.name}`;
            let fileRef;
            
            // Check if the file is an image
            isImage(file).then((result) => {
                if (result) {
                    // If it's an image, upload to the 'images' folder
                    fileRef = storageRef.child(`images/${fileName}`);
                } else {
                    // If it's not an image, upload to the 'files' folder
                    fileRef = storageRef.child(`files/${fileName}`);
                }
            
                const uploadTask = fileRef.put(file);
            
                uploadTask.then(() => {
                    // Get the download URL for the file
                    fileRef.getDownloadURL().then((downloadURL) => {
                        messagetextData.type = result ? "image" : "file";
                        messagetextData.uri = downloadURL;

                        console.log(messagetextData);
						// Add the message to Firestore
						groupRef.add(messagetextData);

						// Clear the file input
						fileInput.value = '';
						messageInput.value = '';
                        replyInput.innerHTML = '';
                        loader.style.display = 'none';
                        paperClip.style.display = 'block';
                        fileInput.style.display = 'block';
                    });
                }).catch((error) => {
                    // Handle errors during the upload
                    console.error('Error uploading file:', error);
                });
            });
        } else {
            // Add the message to Firestore (without image)
            // console.log(messagetextData);
            groupRef.add(messagetextData);
            
            messageInput.value = '';
            messageInput.style.display = 'none';
            loaderSending.style.display = 'block';
        }
        messageInput.style.display = 'block';
        loaderSending.style.display = 'none';
        replyDiv.style.display = 'none';
        replyInput.innerHTML = '';
        replyTextId="";


    }
	currentDate = null;

    return false;
}

// Function to render messages
function renderMessages(messages) {

  const chatBox = document.getElementById('chat-box');
  chatBox.innerHTML = ''; 
  currentDate=null;
  messages.forEach((message) => {
 
  const messageDiv = document.createElement('div');
  
      let messageHtml="";
      
        // console.log(message.createdAt);

        let currentDateTime = new Date(message.createdAt);
        const options1= formatISTDate();
        // console.log(istTimeString);
        let currentDateTimeData = currentDateTime.toLocaleString('en-US', options1);
        let currentDateTime1 = new Date(currentDateTimeData);

        // Subtract 5 hours
        currentDateTime1.setHours(currentDateTime1.getHours() - 5);
        
        // Subtract 30 minutes
        currentDateTime1.setMinutes(currentDateTime1.getMinutes() - 30);
        
        // Extract hours and minutes
        const hours = String(currentDateTime1.getHours()).padStart(2, '0');
        const minutes = String(currentDateTime1.getMinutes()).padStart(2, '0');

        // Check if the message date is different from the current date
        if (currentDateTime1.toDateString() !== currentDate) {
        // Display the date in the center
        messageHtml += `<p style="color:white;text-align:center">${currentDateTime1.toDateString()}</p>`;
        currentDate = currentDateTime1.toDateString();
        }
     
      if(message.author.id=='1'){
       
        if(message.type=='text'){

            if(message.replyText !== ''){
                messageHtml+=`
                    <div class="message-user position-relative" >
                        <a href="#${message.replyTextId}">
                            <div class="msg right-msg mb-0">
                                <div class="msg-bubble-reply">
                                    <div class="msg-info" onclick="replyHighLightText('${message.replyTextId}')">
                                        <div class="msg-replyText" >${message.replyText} </div>
                                        <div class="msg-info-time ">${hours} ${minutes}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                     <div class="msg right-msg">
                         <div class="replybox">
                            <div class="msg-text text-white">${message.text}</div>
                         </div>
                         <button class="delete-icon position-absolute" style="top: 32px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="confirmDelete('${message.message_id}')">
                            <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                        </button>
                        <button class="reply-icon position-absolute" style="top: 8px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.text}')">
                            <i class="fa fa-reply"></i>
                        </button>
						 <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
						if(message.chat_pinned=='1'){
							messageHtml+=`<i class="fa fa-bookmark" ></i>`;
						}else{
							messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
						}
					  messageHtml+=` 
                     </div>
                 </div>
               `;
            }else{
				
                messageHtml+=`
                <div class="message-user position-relative" id="${message.message_id}">
                     <div class="msg right-msg">
                         <div class="msg-bubble1">
                         <div class="msg-info">
                             <div class="msg-info-name">${message.name}</div>
                             <div class="msg-info-time">${hours} ${minutes}</div>
                         </div>
                         <div class="msg-text msg-text-${message.message_id}">${message.text}</div>
                        
                         <button class="delete-icon position-absolute" style="top: 32px; width: 23px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="confirmDelete('${message.message_id}')">
                             <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                         </button>
                         <button class="reply-icon position-absolute" style="top: 8px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.text}')">
                             <i class="fa fa-reply"></i>
                         </button>
                         <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
						if(message.chat_pinned=='1'){
							messageHtml+=`<i class="fa fa-bookmark" ></i>`;
						}else{
							messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
						}
					  messageHtml+=`  
                         </button>
                         </div>
                     </div>
                 </div>
               `;
            }
            
        
        }else if(message.type=='image'){

            messageHtml+=`
              <div class="message-client-img-right position-relative" id="${message.message_id}" style="margin-top: 21px;">
                   
                  <a target="_blank" href="${message.uri}"> 
                  <div class="text-white msg-file-${message.message_id}" style=" text-align: center;padding: 7px;border-radius: 15px;border-bottom-left-radius: 0;background: #183153;border-bottom-right-radius: 0;color: #fff !important;"> ${message.filename} </div> 
                  <img src="${message.uri}" style="margin-top: 0px;"> 
                  </a>
                  <button class="delete-icon position-absolute" style="top: 35px;width: 24px;right:-24px;background:#fff;border:none;border-radius:3px;" onclick="confirmDelete('${message.message_id}')"><i class="fa fa-trash text-danger" aria-hidden="true"></i></button>
                  <button class="reply-icon position-absolute" style="top: 8px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.filename}')">
                    <i class="fa fa-reply"></i>
                  </button>
				   <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 61px; width: 24px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
					if(message.chat_pinned=='1'){
						messageHtml+=`<i class="fa fa-bookmark" ></i>`;
					}else{
						messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
					}
				  messageHtml+=` 
              </div>
            `;
        }else if(message.type=='file'){

            messageHtml+=`
			<div style="justify-content: end;display: flex;margin-bottom: 18px; margin-bottom: 20px;margin-top: 20px;" id="${message.message_id}" >
                <div class="download-client-right mb-3 position-relative" style="width:auto;">
                
                    <div class="downloadfile">
                        <div class="filenames msg-pdf-${message.message_id}"> <span>${message.filename}</span> </div>
                        <a href="${message.uri}" target="_blank"> <i class="fas fa-download ps-3"></i> Download </a> 
                    </div>
                    <button class="delete-icon position-absolute" style="width: 24px;top: 29px;right:-24px;background:#fff;border:none;border-radius:3px;" onclick="confirmDelete('${message.message_id}')"><i class="fa fa-trash text-danger" aria-hidden="true"></i></button>
                    <button class="reply-icon position-absolute" style="top: 5px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.filename}')">
                        <i class="fa fa-reply"></i>
                    </button>
                    <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
                            
                    if(message.chat_pinned=='1'){
                        messageHtml+=`<i class="fa fa-bookmark" ></i>`;
                    }else{
                        messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
                    }
                messageHtml+=` 
                </div>
			</div >
            `;
        }
        
      }else{

        if(message.type=='text'){
            if(message.replyText !== ''){
                messageHtml+=`
                <div class="message-user position-relative">
                    <a href="${message.replyTextId}">
                        <div class="msg left-msg mb-0">
                            <div class="msg-bubble-reply">
                                <div class="msg-info ">
                                    <div class="msg-replyText">${message.replyText}</div>
                                    <div class="msg-info-time ">${hours} ${minutes}</div>
                                </div> 
                            </div>
                        </div>
                     </a>
                     <div class="msg left-msg">
                         <div class="replybox">
                            <div class="msg-text text-white">${message.text}</div>
                         </div>
                        <button class="delete-icon position-absolute" style="top: 32px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="confirmDelete('${message.message_id}')">
                            <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                        </button>
                        <button class="reply-icon position-absolute" style="top: 8px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.text}')">
                            <i class="fa fa-reply"></i>
                        </button>
						 <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
						if(message.chat_pinned=='1'){
							messageHtml+=`<i class="fa fa-bookmark" ></i>`;
						}else{
							messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
						}
					  messageHtml+=` 
                     </div>
                 </div>
               `;
            }else{
                messageHtml+=`
                <div class="message-user position-relative" id="${message.message_id}">
                     <div class="msg left-msg">
                         <div class="msg-bubble1">
                         <div class="msg-info">
                             <div class="msg-info-name">${message.name}</div>
                             <div class="msg-info-time">${hours} ${minutes}</div>
                         </div>
                         <div class="msg-text msg-text-${message.message_id}">${message.text}</div>
                         <button class="delete-icon position-absolute" style="top: 32px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="confirmDelete('${message.message_id}')">
                             <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                         </button>
                         <button class="reply-icon position-absolute" style="top: 8px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.text}')">
                             <i class="fa fa-reply"></i>
                         </button>
						  <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
							if(message.chat_pinned=='1'){
								messageHtml+=`<i class="fa fa-bookmark" ></i>`;
							}else{
								messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
							}
						  messageHtml+=` 
                         </div>
                     </div>
                 </div>
               `;
            }
        }else if(message.type=='image'){

            messageHtml+=`
              <div class="message-client-img-left position-relative" id="${message.message_id}" style="margin-top: 21px;">
                  <a target="_blank" href="${message.uri}">  
                  <div class="text-white msg-file-${message.message_id}" style=" text-align: center;padding: 7px;border-radius: 15px;border-bottom-left-radius: 0;background: #183153;border-bottom-right-radius: 0;color: #fff !important;"> ${message.filename} </div> 
                  <img src="${message.uri}" style="margin-top: 0px;"> 
                  </a>
                  <button class="delete-icon position-absolute" style="top: 28px;left:-24px;background:#fff;border:none;border-radius:3px;padding: 2px 7px;" onclick="confirmDelete('${message.message_id}')"><i class="fa fa-trash text-danger" aria-hidden="true"></i></button>
                  <button class="reply-icon position-absolute" style="top: 0px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.filename}')">
                    <i class="fa fa-reply"></i>
                  </button>
				   <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
					if(message.chat_pinned=='1'){
						messageHtml+=`<i class="fa fa-bookmark" ></i>`;
					}else{
						messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
					}
				  messageHtml+=` 
              </div>
            `;
        }else if(message.type=='file'){

            messageHtml+=`
			<div style="justify-content: start;display: flex;" id="${message.message_id}" >
            <div class="download-client-left mb-3 position-relative">
                <div class="filenames msg-pdf-${message.message_id}"><span>${message.filename}</span> </div>
                <div class="downloadfile"><a href="${message.uri}" target="_blank"> <i class="fas fa-download ps-3"></i> Download </a> </div>
                <button class="delete-icon position-absolute" style="top: 25px;left:-24px;background:#fff;border:none;border-radius:3px;" onclick="confirmDelete('${message.message_id}')"><i class="fa fa-trash text-danger" aria-hidden="true"></i></button>
                <button class="reply-icon position-absolute" style="top: 2px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="replyToMessage('${message.message_id}', '${message.name}', '${message.filename}')">
                    <i class="fa fa-reply"></i>
                </button>
				 <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 55px; width: 22px; left: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
					 
					if(message.chat_pinned=='1'){
						messageHtml+=`<i class="fa fa-bookmark" ></i>`;
					}else{
						messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
					}
				  messageHtml+=` 
            </div>
			</div>
            `;
        }
      }

      messageDiv.innerHTML=messageHtml;

      chatBox.appendChild(messageDiv);


  });
  
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Function to check if the file is an image
function isImage(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onloadend = function () {
            const arr = (new Uint8Array(reader.result)).subarray(0, 4);
            let header = '';
            for (let i = 0; i < arr.length; i++) {
                header += arr[i].toString(16);
            }

            // Check the file header to determine the file type
            const isImage = /^89504e47|^ffd8ffe0|^ffd8ffe1|^ffd8ffe2|^ffd8ffe3|^ffd8ffe8/.test(header);
            resolve(isImage);
        };
        reader.readAsArrayBuffer(file);
    });
}

function deleteMessage(messageId) {

    // Reference to the specific message document
    const messageRef = groupRef.doc(messageId);

    // Delete the message
    messageRef.delete()
        .then(() => {
            console.log("Message deleted successfully");
        })
        .catch((error) => {
            console.error("Error deleting message: ", error);
    });
}

function confirmDelete(messageId) {
    const confirmation = confirm("Are you sure you want to delete this message?");
    if (confirmation) {
        deleteMessage(messageId);
    }
}


function replyToMessage(messageId, senderName, originalMessage) {

    const replyInput = document.getElementById('reply-input');

    replyMsgId=messageId;

    if (replyInput) {
 
      // Check if the reply input already has content
      const existingContent = replyInput.innerText.trim();
     
      // Construct the reply message with mention
    //   const mention = `@${senderName} `;
      const replyMessage = existingContent ? `${existingContent}` : `${originalMessage}`;
  
      // Set the reply input with the composed reply
      replyInput.innerText = replyMessage;
  
      // Toggle the display property based on content
      replyDiv.style.display = replyMessage ? 'block' : 'none';
    } else {
      console.error('Element with ID "reply-input" not found in the DOM.');
    }
}

function closeReplyDiv() {
 
    const replyInput = document.getElementById('reply-input');
    const replyDiv = document.getElementById('replyDiv');

    if (replyInput && replyDiv) {

        replyInput.innerHTML = '';

        replyDiv.style.display = 'none';
    } else {
        console.error('Element with ID "reply-input" or "replyDiv" not found in the DOM.');
    }

}

function replyHighLightText(replyTextId) {
    const msgTextElement = document.querySelector(`.msg-text-${replyTextId}`);
    const fileTextElement = document.querySelector(`.msg-file-${replyTextId}`);
    const pdfTextElement = document.querySelector(`.msg-pdf-${replyTextId}`);

    if(fileTextElement){
        fileTextElement.style.background = '#3c5318';
        setTimeout(() => {
            fileTextElement.style.background = '#183153';
        }, 5000);
    }
    if (msgTextElement) {
        msgTextElement.setAttribute('id', 'order1');
        setTimeout(() => {
            msgTextElement.removeAttribute('id', 'order1');
        }, 5000);
    } 
    if (pdfTextElement) {
        pdfTextElement.style.background = '#3c5318';
        setTimeout(() => {
            pdfTextElement.style.background = '';
        }, 5000);
    }
}





// Function to display all bookmarked messages
async function showAllPinnedMessages() {
	
	try {
		
		// Toggle the modals
		$('#bookMark').modal('toggle');
		$('#exampleModal').modal('hide');
 
 
		groupRef = db.collection('Chat').doc(_groupId).collection('message');
			
		// Real-time listener for new messages
		groupRef.where('chat_pinned', '==','1').onSnapshot((snapshot) => {
			console.log(snapshot);
			const messages = snapshot.docs.map((doc) => {
				return {
					message_id: doc.id,
					...doc.data(),
				};
			});
			renderMessagesPinned(messages);
		});

		 
	} catch (error) {
		console.error('Error displaying pinned messages:', error);
	}
}

function renderMessagesPinned(messages) {

  const chatBox = document.getElementById('pinChat-box');
  chatBox.innerHTML = ''; 
  currentDate=null;
  messages.forEach((message) => {
 
  const messageDiv = document.createElement('div');
  
      let messageHtml=""; 
        if(message.type=='text'){
			messageHtml+=`
			<div class="message-user position-relative">
                <div class="msg right-msg">
                    <div class="msg-bubble1">
                            <a href="#redirect-to-msg${message.message_id}" onclick="showChatModel('${message.message_id}')" style="color: white;">
                                <div class="msg-text ">${message.text}</div>
                            </a>
                    
                            <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 18px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
                        
                            if(message.chat_pinned=='1'){
                                messageHtml+=`<i class="fa fa-bookmark" ></i>`;
                            }else{
                                messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
                            }
                        messageHtml+=` 
                    </div>
                </div>
			 </div>
		   `; 
        }else if(message.type=='image'){

            messageHtml+=`
              <div class="message-client-img-right position-relative" id="${message.message_id}">
                    <a href="#redirect-to-msg${message.message_id}" onclick="showChatModel('${message.message_id}')">
                        <div class="text-white" style=" text-align: center;padding: 7px;border-radius: 15px;border-bottom-left-radius: 0;background: #183153;border-bottom-right-radius: 0;color: #fff !important;"> ${message.filename} </div> 
                        <img src="${message.uri}" style="margin-top: 0px;"> 
                    </a>
				   <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 32px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
						 
					if(message.chat_pinned=='1'){
						messageHtml+=`<i class="fa fa-bookmark" ></i>`;
					}else{
						messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
					}
				  messageHtml+=` 
              </div>
            `;
        }else if(message.type=='file'){

            messageHtml+=`
			<div style="justify-content: end;display: flex;" id="${message.message_id}">
                <div class="download-client-left mb-3 position-relative">
                    <div class="filenames"><span onclick="showChatModel('${message.message_id}')">${message.filename}</span> </div>
                    <div class="downloadfile"><a href="${message.uri}" > <i class="fas fa-download ps-3"></i> Download </a> </div>
                    
                    <button class="position-absolute msg-bookmark-${message.message_id}" style="top: 18px; width: 22px; right: -24px; background: #fff; border: none; border-radius: 3px;" onclick="toggleChatPinned('${message.message_id}','${message.chat_pinned}')">`;
                        
                        if(message.chat_pinned=='1'){
                            messageHtml+=`<i class="fa fa-bookmark" ></i>`;
                        }else{
                            messageHtml+=`<i class="fa fa-bookmark-o" ></i>`;
                        }
                    messageHtml+=` 
                </div>
			</div>
            `;
        }
     
      messageDiv.innerHTML=messageHtml;

      chatBox.appendChild(messageDiv);


  });
  
  chatBox.scrollTop = chatBox.scrollHeight;
}


async function showChatModel(messageId) {
    const msgTextElement = document.querySelector(`.msg-text-${messageId}`);
    const fileTextElement = document.querySelector(`.msg-file-${messageId}`);
    const pdfTextElement = document.querySelector(`.msg-pdf-${messageId}`);
    
    
    if(fileTextElement){
        fileTextElement.style.background = '#3c5318';
        setTimeout(() => {
            fileTextElement.style.background = '#183153';
        }, 5000);
    }
    if (msgTextElement) {
        msgTextElement.setAttribute('id', 'order1');
        setTimeout(() => {
            msgTextElement.removeAttribute('id', 'order1');
        }, 5000);
    } 

    if (pdfTextElement) {
        pdfTextElement.style.background = '#3c5318';
        setTimeout(() => {
            pdfTextElement.style.background = '';
        }, 5000);
    }
    $('#bookMark').modal('hide');
    $('#exampleModal').modal('toggle');
 
}



  
  

