FROM php:8.2-apache

# تغيير مسار الجذر إلى مجلد public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# نسخ جميع الملفات إلى داخل الحاوية
COPY . /var/www/html/

# تمكين الـ rewrite module (ضروري لبعض الإعدادات)
RUN a2enmod rewrite

# فتح المنفذ 80
EXPOSE 80

# تشغيل Apache
CMD ["apache2-foreground"]
