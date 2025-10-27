네, `README.md` 파일용 DB 구축 방법을 간결한 말투와 마크다운 형식으로 다시 드릴게요.

-----

# ⚾ team08 야구 빅데이터 분석 프로젝트 ⚾

## 1\. 프로젝트 환경

  * **Server:** XAMPP (Apache, MariaDB)
  * **Language:** PHP, SQL
  * **Database:** `team08`
  * **Account(ID/PW):** `team08` / `team08`

-----

## 2\. DATABASE (MariaDB) 구축 방법

### 1단계: XAMPP 서버 실행

1.  XAMPP Control Panel 실행.
2.  `Apache`와 `MySQL` 모듈 **[Start]** 버튼 클릭.

### 2단계: DB 생성 및 데이터 로드 (CMD 사용)

1.  Windows CMD(명령 프롬프트) 실행.

2.  프로젝트의 `sql` 폴더로 이동. (이하 예시 경로)

    ```cmd
    cd C:\xampp\htdocs\team08\sql
    ```

3.  `team08` 계정으로 `team08` 데이터베이스에 접속.

    > **[중요]** `LOAD DATA` 오류(Error 2068) 방지를 위해 `--local-infile=1` 플래그를 **반드시 포함**해야 함.

    ```cmd
    mysql -u team08 -p --local-infile=1 team08
    ```

4.  비밀번호(`team08`) 입력.

5.  `mysql>` 프롬프트가 뜨면, `dbcreate.sql`을 실행하여 테이블 생성.

    ```sql
    mysql> source dbcreate.sql;
    ```

6.  `dbinsert.sql`을 실행하여 CSV 원본 데이터 로드. (데이터 양에 따라 시간 소요)

    ```sql
    mysql> source dbinsert.sql;
    ```

7.  `exit;`를 입력하여 종료.

-----

## 3\. ※ (필독) `LOAD DATA` 오류 (Error 2068) 해결 방법

2단계의 3번 명령어(`mysql ... --local-infile=1 ...`)를 실행했음에도 `ERROR 2068`이 발생한다면, 접근 권한 설정이 추가로 필요함.

### 1\. 서버 (XAMPP) 설정

1.  XAMPP Control Panel \> MySQL 모듈 \> **[Config]** \> **`my.ini`** 파일 열기.
2.  `[mysqld]` 섹션 아래에 다음 한 줄 추가.
    ```ini
    local-infile=1
    ```
    (※ `local_infile=1`이 아닌 `local-infile=1` 일 수 있음. `my.ini` 파일 내 다른 옵션들 표기법 참고)
3.  파일 저장 후, XAMPP의 **MySQL** 서버 **[Stop]** -\> **[Start]** (재시작).

### 2\. (선택) MySQL Workbench 설정

MySQL Workbench를 사용하여 `dbinsert.sql` 스크립트를 직접 실행할 경우 이 설정이 필요함.

1.  Workbench에서 `team08` DB 연결(Connection) 아이콘 우클릭 \> **[Edit Connection...]** 선택.
2.  **[Advanced]** 탭 클릭.
3.  `Others:` 텍스트 박스 안에 아래 내용 추가.
    ```
    OPT_LOCAL_INFILE=1
    ```
4.  창을 닫고 다시 접속하면 `LOAD DATA` 스크립트가 Workbench에서도 실행됨.
