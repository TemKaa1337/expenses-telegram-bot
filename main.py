import telebot
from key import getKey

stickerJabka = 'CAACAgIAAxkBAAMMXmZd9840pEvyOeI8IQ7rkJ8iDvYAAlYAA6tXxAsp0boInqNcqRgE'

bot = telebot.TeleBot(getKey())

@bot.message_handler(commands=['start'])
def startMessage(message):
    bot.send_message(message.chat.id, 'Привет, ты написал мне /start')

@bot.message_handler(content_types=['text'])
def sendText(message):
    if 'люблю' in message.text.lower():
    	bot.send_sticker(message.chat.id, stickerJabka)

bot.polling()