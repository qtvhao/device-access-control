# Device Access Control

## Introduction

Device Access Control là một module cho phép quản lý thiết bị và kiểm soát truy cập vào hệ thống dựa trên thiết bị. Module này cung cấp các chức năng thêm mới thiết bị, kiểm tra giới hạn thiết bị, và lưu trữ thông tin thiết bị cho từng người dùng.

### Các tính năng

- Quản lý thiết bị: Thêm, lưu, và tìm kiếm thông tin thiết bị dựa trên user_id, device_id, và device_type.
- Kiểm soát truy cập: Kiểm tra giới hạn thiết bị và kiểm tra quyền truy cập vào hệ thống.
- Caching: Tối ưu hóa hiệu suất truy cập thiết bị bằng cơ chế caching.
- Migration: Dễ dàng cài đặt cấu trúc bảng devices qua Laravel migration.

### Cài đặt


Các bước cài đặt

1.	Thêm repository vào composer.json:
Mở tệp composer.json của dự án và thêm repository:
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/qtvhao/device-access-control"
        }
    ],


2.	Thêm package vào phần require:
    "require": {
        "qtvhao/device-access-control": "dev-main"
    }

3.	Chạy Composer để cài đặt module:

    composer update

4.	Xuất bản migration:
Chạy lệnh này để xuất bản các tệp migration cho bảng devices:

    php artisan vendor:publish --tag=device-access-control-migrations


5.	Chạy migration:

    php artisan migrate

6.	Cấu hình caching:
Nếu muốn sử dụng caching, hãy đảm bảo đã cấu hình cache driver trong .env, ví dụ:

```dotenv
    CACHE_DRIVER=redis
```

Cách sử dụng

1. Thêm mới thiết bị

Để thêm một thiết bị mới, sử dụng AddNewDeviceUseCase. Ví dụ:

```php
    $deviceData = new DeviceData([
        'user_id' => 1,
        'device_id' => 'device-12345',
        'device_type' => 'mobile',
    ]);
    $addNewDeviceUseCase->execute($deviceData);
```
2. Kiểm tra thiết bị đã tồn tại

Sử dụng CheckExistingDeviceUseCase để kiểm tra thiết bị của người dùng:
```php
    $deviceExists = $checkExistingDeviceUseCase->execute($deviceId);
```
3. Kiểm tra giới hạn thiết bị

Sử dụng CheckDeviceLimitUseCase để kiểm tra nếu thiết bị mới vượt quá giới hạn:

```php
$canAddDevice = $checkDeviceLimitUseCase->execute($userId, $deviceType);
```
### Cấu trúc dự án
- **Device**: Model quản lý thông tin thiết bị (device_id, device_type, user_id).
- **Repositories**: Các repository xử lý truy vấn dữ liệu cho thiết bị.
- **Use Cases**: Lớp logic nghiệp vụ, bao gồm AddNewDeviceUseCase, CheckExistingDeviceUseCase, và CheckDeviceLimitUseCase.
- **Caching**: DeviceAccessCacheDecorator để cache dữ liệu truy cập thiết bị.

Testing

1.	Chạy PHPUnit:

    php artisan test

2.	Kiểm tra cụ thể một UseCase:
Các kiểm thử cho từng use case như AddNewDeviceUseCase và CheckDeviceLimitUseCase đã được cấu hình trong thư mục tests.

### Đóng góp

Nếu bạn muốn đóng góp cho dự án, hãy làm theo các bước sau:

1. Fork repository.
2. Tạo một nhánh mới: `git checkout -b my-feature`.
3. Commit các thay đổi của bạn: `git commit -m 'Add a new feature'`.
4. Push lên nhánh của bạn: `git push origin my-feature`.
5. Tạo một pull request trên GitHub.

### Liên hệ

Nếu có thắc mắc hoặc gặp vấn đề, vui lòng liên hệ qua email [qtvhao@gmail.com] hoặc mở một issue trên GitHub.

### License

Dự án này được phát hành theo giấy phép MIT.
