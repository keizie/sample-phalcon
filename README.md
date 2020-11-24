# sample-phalcon

- API 문서 : https://documenter.getpostman.com/view/7103288/TVewajjX
- 실행
  1. docker-compose build
  2. docker-compose up
  3. `/app/migrations/*.sql` 파일을 docker mysql에 반영하여 테이블 생성
  4. 회원가입 API를 몇 차례 호출해서 회원 생성
  5. `/index/dummyOrder` URL에 접근해서 100개씩 주문 생성 (실행 시점의 회원들에 임의로 할당됨)
  6. 제공된 Postman json을 import하여 호출 테스트 (URL은 docker-compose 실행 호스트에 맞게 변경)
- 기타
  * PHP 7.4, Phalcon 4.1
  * docker 구성 과정에서 php 공식 이미지가 deb 패키지 설치를 허용하지 않아 phalcon 최초 빌드에 시간이 걸림
  * 타임존은 `php.ini` 파일 대신 `config.php` 파일에서 변경
  * 회원 전화번호에서 숫자만 남기는 과정을 서버에서 항상 적용함. 프론트엔드에서는 사용자가 인지할 수 있도록 정보 제공하는 것으로 충분
  * 회원 성별은 최근 선택지를 넓게 두는 경향이 있으므로 별도의 검사를 하지 않음. 필요시 정제해 사용하고 추가 로직 적용
  * 주문번호는 UNIQUE 인덱스를 사용하는 대신 별도의 PK를 유지하여, 해시 중복이 발생해도 우선 주문을 진행하고 사후 교정할 수 있게 함
  * 주문 결제일시는 지역 시각과 UTC 시각을 모두 기록함. 사용자 출력시에는 지역 시각 참조하면 됨
  * `admin_user` 테이블은 회원별 관리권한 부여할 경우 사용할 용도이나 현재 미사용. 권한 분화할 경우 필드 추가