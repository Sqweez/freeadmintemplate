from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import sys


def function(text):
    chrome_options = Options()
    chrome_options.headless = True
    chrome_options.add_experimental_option("excludeSwitches", ["enable-logging"])

    driver = webdriver.Chrome(options=chrome_options)

    url = "https://translate.yandex.ru/?source_lang=ru&target_lang=en"
    driver.get(url)

    try:
        textarea = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, '//*[@id="fakeArea"]')))
        textarea.clear()
        textarea.send_keys(text)


        element = driver.find_element(By.XPATH, '//*[@id="textbox2"]/div[1]/div[1]')
        element.location_once_scrolled_into_view

        WebDriverWait(driver, 10).until(EC.presence_of_al_elements_located((By.XPATH, '//*[@id="translation"]')))
        spinner = '//*[@id="textbox2"]/div[1]/div[2]/span'
        WebDriverWait(driver, 10).until(EC.invisibility_of_element_located((By.XPATH, spinner)))

        review_text_element = WebDriverWait(driver, 10).until(EC.presence_of_element_located((By.XPATH, '//*[@id="translation"]/span')))
        review_text = review_text_element.text
        print(review_text.strip())
        driver.quit()


    except Exception as e:
        print("")


if __name__ == "__main__":
    input_text = sys.argv[1]
    function(input_text)
