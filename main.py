import telebot
from key import key

stickerJabka = 'CAACAgIAAxkBAAMMXmZd9840pEvyOeI8IQ7rkJ8iDvYAAlYAA6tXxAsp0boInqNcqRgE'

bot = telebot.TeleBot(key)

@bot.message_handler(commands=['start'])
def start_message(message):
    bot.send_message(message.chat.id, 'Привет, ты написал мне /start')

@bot.message_handler(content_types=['text'])
def send_text(message):
    if 'люблю' in message.text.lower():
    	bot.send_sticker(message.chat.id, stickerJabka)

bot.polling()